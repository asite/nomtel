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
}
