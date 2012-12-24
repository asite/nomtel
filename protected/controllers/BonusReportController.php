<?php

define('OPERATOR_BEELINE_ID', 1);

class BonusReportController extends BaseGxController
{

    public function actionDelete($id)
    {
        if (Yii::app()->getRequest()->getIsPostRequest() && isAdmin()) {
            $trx = Yii::app()->db->beginTransaction();

            $bonusReports = Yii::app()->db->createCommand("select agent_id,sum-sum_referrals from bonus_report_agent where bonus_report_id=:bonus_report_id")->
                queryAll(true, array(':bonus_report_id' => $id));

            $cmd = Yii::app()->db->createCommand("update agent set balance=balance-:sum where id=:id");
            foreach ($bonusReports as $report)
                $cmd->execute(array(':id' => $report['agent_id'], ':sum' => $report['sum']));

            // get payment ids for deletion
            $paymentIds=Yii::app()->db->createCommand(
                "select payment_id from bonus_report_agent
                 where bonus_report_agent.bonus_report_id=:bonus_report_id")->queryColumn(array(':bonus_report_id'=>$id));

            BonusReportAgent::model()->deleteAllByAttributes(array('bonus_report_id'=>$id));
            Payment::model()->deleteByPk($paymentIds);

            BonusReport::model()->deleteByPk($id);

            $trx->commit();

            if (!Yii::app()->getRequest()->getIsAjaxRequest())
                $this->redirect(array('list'));
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    public function actionView($id)
    {
        $model = new BonusReportAgent('list');
        $model->unsetAttributes();

        if (isset($_GET['BonusReportAgent']))
            $model->setAttributes($_GET['BonusReportAgent']);

        $dataProvider = $model->search();
        $dataProvider->criteria->alias = 'bra';
        $dataProvider->criteria->join = 'join bonus_report br on (br.id=bra.bonus_report_id) '.
            'join agent a on (a.id=bra.agent_id)';
        $dataProvider->criteria->addColumnCondition(array('bonus_report_id'=>$id,'a.parent_id'=>loggedAgentId()));

        $this->render('view', array(
            'model' => $model,
            'dataProvider' => $dataProvider,
            'bonusReport' => BonusReport::model()->findByPk($id),
            'bonusReportAgent' =>BonusReportAgent::model()->findByAttributes(array(
                'bonus_report_id'=>$id,
                'agent_id'=>loggedAgentId()
            ))
        ));

    }

    public function actionList()
    {
        $model = new BonusReport('list');
        $model->unsetAttributes();

        if (isset($_GET['BonusReport']))
            $model->setAttributes($_GET['BonusReport']);

        $dataProvider = $model->search();

        $dataProvider->criteria->alias = 'br';
        $dataProvider->criteria->join = 'join bonus_report_agent bra on (bra.bonus_report_id=br.id and ' .
            'bra.agent_id='.loggedAgentId(). ')';

        $this->render('list', array(
            'model' => $model,
            'dataProvider' => $dataProvider
        ));
    }

    private function error($msg) {
        Yii::app()->user->setFlash('error',$msg);
        $this->redirect(array());
    }

    private function errorInvalidFormat($msg) {
        $this->error(Yii::t('app','File has invalid format (%msg%)',array('%msg%'=>$msg)));
    }

    private function roundSum($sum) {
        return floor($sum*100)/100;
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
        $agents[adminAgentId()]['payments']=array(array('agent_id'=>adminAgentId(),'rate'=>$agents[adminAgentId()]['rate']));

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

                    // check rate validity
                    $idx=count($agents[$k]['payments'])-1;
                    if ($agents[$k]['payments'][$idx]['rate']>$agents[$k]['payments'][$idx-1]['rate']) {
                        $agent1=Agent::model()->findByPk($agents[$k]['payments'][$idx-1]['agent_id']);
                        $agent2=Agent::model()->findByPk($agents[$k]['payments'][$idx]['agent_id']);
                        $this->error(Yii::t('app',"Agent %agent1% has rate lower than subagent %agent2%",array('%agent1%'=>$agent1,'%agent2%'=>$agent2)));
                    }

                    $modified=true;
                }
        } while ($modified);

        // calculate earned bonuses+statistics
        $agentsBonuses=array();
        foreach($simAgent as $v) {
            $bonus=$simBonus[$v['sim_id']];

            $lastPaymentIndex=count($agents[$v['agent_id']])-1;
            foreach($agents[$v['agent_id']]['payments'] as $i=>$payment) {
                $agentsBonuses[$payment['agent_id']]['sim_count']++;
                $agentsBonuses[$payment['agent_id']]['sum']+=$this->roundSum($bonus*$payment['rate']);

                // for not last item we must substract provision of child agent
                $agentsBonuses[$payment['agent_id']]['sum_referrals']+= $i==$lastPaymentIndex ? 0:
                    $this->roundSum($bonus*$agents[$v['agent_id']]['payments'][$i+1]['rate']);
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

        $payment=new Payment();
        $payment->type=Payment::TYPE_BONUS;
        $payment->dt=new EDateTime();
        $payment->comment=Yii::t('app','Bonuses')." '".$comment."'";

        $bonusReportAgent=new BonusReportAgent();
        $bonusReportAgent->bonus_report_id=$bonusReport->id;
        $bonusReportAgent->payment_id=$payment->id;

        foreach($agentsBonuses as $agent_id=>$data) {
            $cmdBalance->execute(array(
                ':agent_id'=>$agent_id,
                ':sum'=>$data['sum']-$data['sum_referrals']
            ));

            unset($payment->id);
            $payment->isNewRecord=true;

            $payment->agent_id=$agent_id;
            $payment->sum=$data['sum']-$data['sum_referrals'];
            $payment->save();

            unset($bonusReportAgent->id);
            $bonusReportAgent->isNewRecord=true;

            $bonusReportAgent->sim_count=$data['sim_count'];
            $bonusReportAgent->sum=$data['sum'];
            $bonusReportAgent->sum_referrals=$data['sum_referrals'];
            $bonusReportAgent->payment_id=$payment->id;
            $bonusReportAgent->agent_id=$agent_id;
            $bonusReportAgent->save();
        }

        $trx->commit();

        $this->redirect(array('view','id'=>$bonusReport->id));
    }

    private function processLoadBeeline($model,$reader,$file) {
        $reader->setReadFilter(new BeelineBonusReportReadFilter);
        $book = $reader->load($file->tempName);
        $sheet = $book->getActiveSheet();

        $rows=$sheet->getHighestRow();

        if ($sheet->getCellByColumnAndRow(3, 14)->getValue()!='CTN')
            $this->errorInvalidFormat(__LINE__);
        if ($sheet->getCellByColumnAndRow(7, 14)->getValue()!='Выручка без учета НДС, руб.')
            $this->errorInvalidFormat(__LINE__);

        $simBonus=array();
        $sum=0;
        for($row=15;$row<=$rows;$row++) {
            $ctn=$sheet->getCellByColumnAndRow(3, $row)->getValue();
            $bonus=$sheet->getCellByColumnAndRow(7, $row)->getValue();

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

        Yii::import('application.vendors.PHPExcel', true);

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
