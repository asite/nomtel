<?php
class MegafonBalanceEmailImporter
{
    const STATUS_ANALYZE_PERIODS=14;

    private $emailProcessor;

    public function __construct() {
        $this->emailProcessor=new EMailProcessor(Yii::app()->params['megafonBalanceEmails']);
    }

    private function parse($mail) {

    }

    private function log($msg,$level=CLogger::LEVEL_INFO) {
        Yii::log($msg,$level,__CLASS__);
    }

    private function recalcNumberBalanceStatus($number) {
        $data=Yii::app()->db->createCommand("
            select brn.balance
            left outer join balance_report_number brn on (brn.number_id=:number_id and brn.balance_report_id=br.id)
            from balance_report br
            order by br.dt desc
            limit 0,".self::STATUS_ANALYZE_PERIODS."
        ")->queryAll(array(':number_id'=>$number->id));

        $allPositive=true;
        $allNegative=true;
        $allStatic=true;

        for($i=count($data)-1;$i>=0;$i--) {
            $curVal=floatval($data[$i]);

            if ($curVal>=0) $allNegative=false; else $allPositive=false;
            if ($i>0 && abs($curVal-$data[$i-1])>1e-4) $allStatic=false;
        }

        $newBalanceStatus=Number::BALANCE_STATUS_NORMAL;

        if ($allStatic) {
            $newBalanceStatus=$allPositive ? Number::BALANCE_STATUS_POSITIVE_STATIC:Number::BALANCE_STATUS_NEGATIVE_STATIC;
        } else {
            if ($allPositive) $newBalanceStatus=Number::BALANCE_STATUS_POSITIVE_DYNAMIC;
            if ($allNegative) $newBalanceStatus=Number::BALANCE_STATUS_NEGATIVE_DYNAMIC;
        }

        if (count($data)==1) $newBalanceStatus=Number::BALANCE_STATUS_NEW;

        if ($newBalanceStatus!=$number->balance_status) {
            $number->balance_status=$newBalanceStatus;
            $number->save();
        }
    }

    private function process($data) {
        $trx=Yii::app()->db->beginTransaction();

        $number=Number::model()->findByAttributes(array('number'=>$data['number']));

        if (!$number) {
            Yii::log("number '{$data['number']}' not found in database",CLogger::LEVEL_WARNING);
            return;
        }

        if ($data['personal_account'] && $number->personal_account!=$data['personal_account']) {
            $number->personal_account=$data['personal_account'];
            $number->save();
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

        $balanceReport->save();

        $this->recalcNumberBalanceStatus($number);

        $trx->commit();

        return true;
    }

    public function importNext() {
        $mail=$this->emailProcessor->fetchMail();
        if ($mail===false) return false;

        $data=$this->parse($mail);
        if ($data!==false) {
            if ($this->process($data)) {
                $this->emailProcessor->deleteMail();
            } else {
                $this->emailProcessor->skipEmail();
            }
        } else {
            $this->emailProcessor->skipEmail();
        }

        return true;
    }
}
