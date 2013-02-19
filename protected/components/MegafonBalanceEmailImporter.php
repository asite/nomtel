<?php
class MegafonBalanceEmailImporter
{
    private $emailProcessor;

    public function __construct() {
        $this->emailProcessor=new EMailProcessor(Yii::app()->params['megafonBalanceEmails']);
    }

    private function parse($mail) {

    }

    private function log($msg,$level=CLogger::LEVEL_INFO) {
        Yii::log($msg,$level,__CLASS__);
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
            'balance_report_id'=>$balanceReport->id;
        ))


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
