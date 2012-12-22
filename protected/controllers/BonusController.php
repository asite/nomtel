<?php

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

    private function calculateBonuses($simBonus,$simAgent,$comment,$operator_id) {
        $db=Yii::app()->db;

        // get agents info
        $agentsRaw=$db->createCommand(
            "select a.parent_id,a.id,arr.rate
            from agent a
            left outer join agent_referral_rate arr on (arr.agent_id=a.id and arr.operator_id=:operator_id)"
        )->queryAll(true,array(':operator_id'=>$operator_id));

        $agents=array();
        foreach($agentsRaw as $row) {
            $agents[$row['id']]=array('rate'=>$row['rate']/100,'parent_id'=>$row['parent_id'] ? $row['parent_id']:0);
        }
        $agents[0]=array('payments'=>array());

        unset($agentsRaw);

        // calculate agents payouts table
        do {
            $modified=false;
            foreach($agents as $k=>$v)
                if (!isset($v['payments']) && isset($agents[$v['parent_id']]['payments'])) {
                    $agents[$k]['payments']=$agents[$v['parent_id']]['payments'];

                    $agents[$k]['payments'][]=array(
                        'agent_id'=>$k,
                        'rate'=>$v['rate']
                        );

                    $modified=true;
                }
        } while ($modified);

        // calculate earned bonuses
        $agentsBonuses=array();
        foreach($simAgent as $v) {
            $bonus=$simBonus[$v['sim_id']];

            $lastPaymentIndex=count($agents[$v['agent_id']])-1;
            foreach($agents[$v['agent_id']]['payments'] as $i=>$payment) {
                $bonus*=$payment['rate'];
                // for not last item we must substract provision of child agent
                $agentsBonuses[$payment['agent_id']]+= $i==$lastPaymentIndex ? $bonus:
                    $bonus*(1-$agents[$v['agent_id']]['payments'][$i+1]['rate']);
            }
        }

        // store all info in database
        $trx=$db->beginTransaction();

        $bonusReport=new BonusReport;
        $bonusReport->dt=new EDateTime();
        $bonusReport->operator_id=$operator_id;
        $bonusReport->comment=$comment;
        $bonusReport->save();

        $cmdBalance=$db->createCommand('update agent set balance=balance+:sum where id=:agent_id');
        $cmdReport=$db->createCommand(
            'insert into payment (agent_id,bonus_report_id,type,dt,comment,sum) values
            (:agent_id,'.$db->quoteValue($bonusReport->id).",'BONUS',NOW(),".
            $db->quoteValue(Yii::t('app','Bonuses')." '".$comment."'").',:sum)');

        foreach($agentsBonuses as $agent_id=>$bonus) {
            $cmdBalance->execute(array(
                ':agent_id'=>$agent_id,
                ':sum'=>$bonus
            ));
            $cmdReport->execute(array(
                ':agent_id'=>$agent_id,
                ':sum'=>$bonus)
            );
        }

        $trx->commit();
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

        $this->calculateBonuses($simBonus,$simAgent,$model->comment,OPERATOR_BEELINE_ID);
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
            case OPERATOR_BEELINE_ID:
                $this->processLoadBeeline($model,$reader,$file);
                break;
            default:
                Yii::app()->user->setFlash('error',Yii::t('app','Loading bonuses for this operator is not yet implemented'));
                $this->redirect(array());
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
