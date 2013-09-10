<?php

class CronController extends Controller
{
    const MAX_WORKING_TIME=50;

    private $endTime;

    public function additionalAccessRules()
    {
        return array(
            array('allow', 'users' => array('*')),
        );
    }

    protected function beforeAction(CAction $action) {
        $this->endTime=time()+self::MAX_WORKING_TIME;
        return true;
    }

    private function haveTime() {
        return time()<$this->endTime;
    }

    public function actionImportMegafonBalanceEmails() {
        // allow only one running instance of this action
        $sem=new Semaphore(__FUNCTION__,1);

        $importer=new MegafonBalanceEmailImporter();

        while ($this->haveTime()) {
            if (!$importer->importNext()) break;
        }
    }

    public function actionCheckNoDataAndClosedBalances() {
        Number::checkNoDataAndClosedBalances();
    }

    public function actionSendMegafonRestoreApplication() {
        $current=MegafonAppRestore::getCurrent();

        $megafonAppRestores=MegafonAppRestore::model()->findAll("dt<:dt and sent_to_email=0",array(':dt'=>$current->dt->toMysqlDate()));
        foreach($megafonAppRestores as $megafonAppRestore) {
            $fileNameMegafonAppRestore=$megafonAppRestore->generateDocument(true);
            $fileNameRestoredReport=$megafonAppRestore->generateUnrestoredReport(true);

            $mail = new YiiMailMessage();
            $mail->setSubject('Заявление на восстановление номеров мегафон №'.$megafonAppRestore->id.' от '.$megafonAppRestore->dt->format('d.m.Y'));
            $mail->attach(Swift_Attachment::fromPath($fileNameMegafonAppRestore));
            $mail->attach(Swift_Attachment::fromPath($fileNameRestoredReport));


            $mail->setFrom(Yii::app()->params['adminEmailFrom']);
            $mail->setTo(Yii::app()->params['megafonAppRestoreEmail']);

            if (Yii::app()->mail->send($mail)) {
                $megafonAppRestore->sent_to_email=true;
                $megafonAppRestore->save();
            }

            unlink($fileNameMegafonAppRestore);
            unlink($fileNameRestoredReport);
        }
    }
}
