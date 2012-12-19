<?php

define('NOMTEL_AGENT_PERCENT',5);
define('OPERATOR_BEELINE_ID',1);

Yii::import('application.vendors.PHPExcel',true);

class BeelineReadFilter implements PHPExcel_Reader_IReadFilter
{
    public function readCell($column, $row, $worksheetName = '') {
        return $column=='D' || $column=='H' || $column=='U';
    }
}

class BonusController extends BaseGxController
{

    private function errorInvalidFormat($msg) {
        Yii::app()->user->setFlash('error',Yii::t('app','File has invalid format (%msg%)',array('%msg%'=>$msg)));
        $this->redirect(array());
    }

    private function calculateBonuses($simBonus,$simAgent) {
        // get agents info
        $agentsRaw=Yii::app()->db->createCommand("select id,parent_id,referral_percent from agent")->queryAll();
        $agents=array();
        foreach($agentsRaw as $row) {
            $agents[$row['id']]=array('referral_percent'=>$row['referral_percent'],'parent_id'=>$row['parent_id'] ? $row['parent_id']:0);
        }
        $agents[0]=array('referral_percent'=>NOMTEL_AGENT_PERCENT,'multiplier'=>1,'multiplier_referral'=>1);

        unset($agentsRaw);

        // calculate agent bonus multiplier
        do {
            $modified=false;
            foreach($agents as $k=>$v)
                if (!isset($v['multiplier']) && isset($agents[$v['parent_id']]['multiplier'])) {
                    $agents[$k]['multiplier']=$agents[$v['parent_id']]['multiplier']*$agents[$v['parent_id']]['referral_percent']/100;
                    $agents[$k]['multiplier_referral']=$agents[$k]['multiplier']*(100-$v['referral_percent'])/100;
                    $modified=true;
                }
        } while ($modified);

        $agentsBonuses=array();
        foreach($simAgent as $v) {
            $bonus=$simBonus[$v['sim_id']];
            $agent_id=$v['agent_id'];
            $field='multiplier';
            while ($agent_id>0) {
                $agentsBonuses[$agent_id]+=$agents[$agent_id][$field]*$bonus;
                $agent_id=$agents[$agent_id]['parent_id'];
                $field='multiplier_referral';
            }
        }

        exit;
    }

    private function processLoadBeeline($model,$reader,$file) {
        $reader->setReadFilter(new BeelineReadFilter);
        $book = $reader->load($file->tempName);
        $sheet = $book->getActiveSheet();

        $rows=$sheet->getHighestRow();

        if ($sheet->getCellByColumnAndRow(3, 14)->getValue()!='CTN')
            $this->errorInvalidFormat(__LINE__);
        if ($sheet->getCellByColumnAndRow(20, 14)->getValue()!='Вознаграждение без НДС, руб.')
            $this->errorInvalidFormat(__LINE__);

        $simBonus=array();
        $sum=0;
        for($row=15;$row<=$rows;$row++) {
            $ctn=$sheet->getCellByColumnAndRow(3, $row)->getValue();
            $bonus=$sheet->getCellByColumnAndRow(20, $row)->getCalculatedValue();

            $sum+=$bonus;

            if ($ctn=='') continue;
            if (!preg_match('%^\d{10}$%',$ctn)) $this->errorInvalidFormat(__LINE__);

            $simBonus[$ctn]=$bonus;
        }

        $book->disconnectWorksheets();
        unset($book);

        if ($sum==0) $this->errorInvalidFormat(__LINE__);

        $db=Yii::app()->db;

        $personal_accounts=array();
        foreach($simBonus as $k=>$v) $personal_accounts[]=$db->quoteValue($k);

        // get agents, to which bonused sims was sent
        $simAgent=$db->createCommand("select personal_account as sim_id,parent_agent_id as agent_id
            from sim
            where operator_id=".OPERATOR_BEELINE_ID." and agent_id is null and personal_account in (".
            implode(',',$personal_accounts).")")->queryAll();

        $this->calculateBonuses($simBonus,$simAgent);
    }

    private function processLoad($model) {
        $file = CUploadedFile::getInstance($model, 'file');

        if ($file === null) {
            Yii::app()->user->setFlash('error',Yii::t('app','File uploaded with error'));
        }

        if ($file->extensionName=='xls') $reader=new PHPExcel_Reader_Excel5;
        if ($file->extensionName=='xlsx') $reader=new PHPExcel_Reader_Excel2007;

        $reader->setReadDataOnly(true);

        switch ($model->operator) {
            case OPERATOR_BEELINE_ID: $this->processLoadBeeline($model,$reader,$file);break;
            default: Yii::app()->user->setFlash('success',Yii::t('app','Loading bonuses for this operator is not yet implemented'));
        }

        Yii::app()->user->setFlash('success',Yii::t('app','Loading bonuses completed successfully'));
        $this->redirect(array(''));
    }

    public function actionLoad() {
        $model=new LoadBonus();

        $this->performAjaxValidation($model);

        if (isset($_POST['LoadBonus'])) {
            $model->setAttributes($_POST['LoadBonus']);

            if ($model->validate()) {
                $this->processLoad($model);
            }
        }

        $this->render('load',array(
            'model'=>$model
        ));
    }
}
