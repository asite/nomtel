<?php
class MegafonBalanceEmailImporter
{
    const STATUS_ANALYZE_PERIODS=14;

    private $emailProcessor;

    public function __construct() {
        $this->emailProcessor=new EMailProcessor(Yii::app()->params['megafonBalanceEmails']);
    }

    private function parseError($mail,$message) {
        Yii::log("parse error:".$message,CLogger::LEVEL_ERROR,'mail_parser');
        return false;
    }

    private function parse($mail) {
        $mail=mb_convert_encoding($mail,'UTF-8','WINDOWS-1251');

        Yii::log("parse mail\n".$mail,CLogger::LEVEL_INFO,'mail_parser');

        $data=array();

        if (!preg_match('%Л/С: (\d{7,8})%',$mail,$m)) return $this->parseError($mail,__LINE__);
        $data['personal_account']=$m[1];
        if (!preg_match('%Номер: (\d{10})%',$mail,$m)) return $this->parseError($mail,__LINE__);
        $data['number']=$m[1];
        if (!preg_match('%Время: (\d\d.\d\d.\d\d\d\d \d\d:\d\d)%',$mail,$m)) return $this->parseError($mail,__LINE__);
        $data['dt']=$m[1];
        if (!preg_match('%На счете ([-.0-9]+) руб\.%',$mail,$m)) return $this->parseError($mail,__LINE__);
        $data['balance']=$m[1];
        if (!preg_match('%Тарифный план: (.*?) с \d\d\.\d\d\.\d\d\d\d\.%',$mail,$m)) return $this->parseError($mail,__LINE__);
        $data['tariff']=$m[1];

        Yii::log("parsed data\n".json_encode($data),CLogger::LEVEL_INFO,'mail_parser');

        return $data;
    }

    private function log($msg,$level=CLogger::LEVEL_INFO) {
        Yii::log($msg,$level,__CLASS__);
    }

    private function recalcNumberBalanceStatus($number) {
        $data=Yii::app()->db->createCommand("
            select brn.balance
            from balance_report br
            left outer join balance_report_number brn on (brn.number_id=:number_id and brn.balance_report_id=br.id)
            order by br.dt desc
            limit 0,".self::STATUS_ANALYZE_PERIODS."
        ")->queryAll(true,array(':number_id'=>$number->id));

        $allPositive=true;
        $allNegative=true;
        $allStatic=true;

        for($i=count($data)-1;$i>=0;$i--) {
            $curVal=floatval($data[$i]['balance']);

            if ($curVal>=0) $allNegative=false; else $allPositive=false;
            if ($i>0 && abs($curVal-floatval($data[$i-1]['balance']))>1e-4) $allStatic=false;
        }

        $newBalanceStatus=Number::BALANCE_STATUS_NORMAL;

        if ($allStatic) {
            $newBalanceStatus=$allPositive ? Number::BALANCE_STATUS_POSITIVE_STATIC:Number::BALANCE_STATUS_NEGATIVE_STATIC;
        } else {
            if ($allNegative) $newBalanceStatus=Number::BALANCE_STATUS_NEGATIVE_DYNAMIC;
            if ($allPositive) $newBalanceStatus=Number::BALANCE_STATUS_POSITIVE_DYNAMIC;
        }

        if (count($data)==1) $newBalanceStatus=Number::BALANCE_STATUS_NEW;

        if ($newBalanceStatus!=$number->balance_status) {
            $number->balance_status=$newBalanceStatus;
            $number->balance_status_changed_dt=new EDateTime();
            $number->save();
        }
    }

    private function process($data) {
        $number=Number::model()->findByAttributes(array('number'=>$data['number']));

        if (!$number) {
            Yii::log("Номер '{$data['number']}' не найден в базе",CLogger::LEVEL_WARNING,'mail_parser');
            return true;
        }

        $trx=Yii::app()->db->beginTransaction();

        $saveNumber=false;

        if ($data['personal_account'] && $number->personal_account!=$data['personal_account']) {
            $number->personal_account=$data['personal_account'];
            Sim::model()->updateAll(array('personal_account'=>$data['personal_account']),'parent_id=:sim_id',array(':sim_id'=>$number->sim_id));
            NumberHistory::addHistoryNumber($number->id,'Личный счет обновлен при импорте e-mail с балансами','База');
            $saveNumber=true;
        }

        if ($saveNumber) $number->save();

        $tariff=Tariff::model()->findByAttributes(array('operator_id'=>Operator::OPERATOR_MEGAFON_ID,'title'=>$data['tariff']));
        if (!$tariff) {
            Yii::log("Неизвестный тариф ".$data['tariff'],CLogger::LEVEL_WARNING,'mail_parser');
        }

        if ($tariff) {
            $sim=Sim::model()->findByPk($number->sim_id);
            if ($sim->tariff_id!=$tariff->id) {
                Sim::model()->updateAll(array('tariff_id'=>$tariff->id),'parent_id=:sim_id',array(':sim_id'=>$number->sim_id));
                NumberHistory::addHistoryNumber($number->id,'Тариф обновлен при импорте e-mail с балансами','База');
            }
        }

        $dt=date('Y-m-d 00:00:00',strtotime($data['dt']));
        $balanceReport=BalanceReport::model()->findByAttributes(array('dt'=>$dt));
        if (!$balanceReport) {
            $balanceReport=new BalanceReport();
            $balanceReport->dt=$dt;
            $balanceReport->comment='E-Mail отчеты за '.date('d.m.Y',strtotime($data['dt']));
            $balanceReport->operator_id=Operator::OPERATOR_MEGAFON_ID;
            $balanceReport->save();
        }

        $balanceReportNumber=BalanceReportNumber::model()->findByAttributes(array(
            'number_id'=>$number->id,
            'balance_report_id'=>$balanceReport->id
        ));

        if (!$balanceReportNumber) {
            $balanceReportNumber=new BalanceReportNumber();
            $balanceReportNumber->balance_report_id=$balanceReport->id;
            $balanceReportNumber->number_id=$number->id;
        }

        $balanceReportNumber->balance=$data['balance'];

        $balanceReportNumber->save();

        $this->recalcNumberBalanceStatus($number);

        $trx->commit();

        return true;
    }

    public function importNext() {
        $mail=$this->emailProcessor->fetchMail();
        if ($mail===false) return false;

        $data=$this->parse($mail);
        Yii::getLogger()->flush(true);

        if ($data!==false) {
            if ($this->process($data)) {
                $this->emailProcessor->emailProcessed();
            } else {
                $this->emailProcessor->emailSkipped();
            }
        } else {
            $this->emailProcessor->emailSkipped();
        }

        return true;
    }
}
