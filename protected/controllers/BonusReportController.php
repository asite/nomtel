<?php

class BonusReportController extends BaseGxController
{
    public function filters()
    {
        return array_merge(parent::filters(), array(
            array('LoggingFilter +load')
        ));
    }

    public function additionalAccessRules()
    {
        return array(
            array('allow', 'actions' => array('list', 'view'), 'roles' => array('agent')),
        );
    }

    public function actionDelete($id)
    {
        if (Yii::app()->getRequest()->getIsPostRequest() && isAdmin()) {
            $trx = Yii::app()->db->beginTransaction();

            $bonusReports = Yii::app()->db->createCommand("select agent_id,sum-sum_referrals from bonus_report_agent where bonus_report_id=:bonus_report_id")->
                queryAll(true, array(':bonus_report_id' => $id));

            foreach ($bonusReports as $report)
                Agent::deltaBalance($report['agent_id'], $report['sum']);

            // get payment ids for deletion
            $paymentIds = Yii::app()->db->createCommand(
                "select payment_id from bonus_report_agent
                 where bonus_report_agent.bonus_report_id=:bonus_report_id")->queryColumn(array(':bonus_report_id' => $id));

            BonusReportAgent::model()->deleteAllByAttributes(array('bonus_report_id' => $id));
            BonusReportNumber::model()->deleteAllByAttributes(array('bonus_report_id' => $id));
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
        $bonusReportAgent = new BonusReportAgent('list');
        $bonusReportAgent->unsetAttributes();

        if (isset($_GET['BonusReportAgent']))
            $bonusReportAgent->setAttributes($_GET['BonusReportAgent']);

        $dataProvider = $bonusReportAgent->search();
        $dataProvider->criteria->alias = 'bra';
        $dataProvider->criteria->join = 'join bonus_report br on (br.id=bra.bonus_report_id) ' .
            'join agent a on (a.id=bra.agent_id)';
        $dataProvider->criteria->addColumnCondition(array('bonus_report_id' => $id, 'a.parent_id' => loggedAgentId()));


        $bonusReportNumberSearch = new BonusReportNumberSearch();
        $bonusReportNumberSearch->unsetAttributes();

        if (isset($_GET['BonusReportNumberSearch']))
            $bonusReportNumberSearch->setAttributes($_GET['BonusReportNumberSearch']);

        $criteria = new CDbCriteria();

        $criteria->compare('brn.bonus_report_id',$id);
        $criteria->compare('n.number', $bonusReportNumberSearch->number, true);
        $criteria->compare('n.personal_account', $bonusReportNumberSearch->personal_account, true);
        $criteria->compare('brn.turnover', $bonusReportNumberSearch->turnover, true);
        $criteria->compare('brn.rate', $bonusReportNumberSearch->rate, true);
        $criteria->compare('brn.sum', $bonusReportNumberSearch->sum, true);
        $criteria->compare('brn.status', $bonusReportNumberSearch->status, true);

        $criteria->compare('brn.parent_agent_id',loggedAgentId());

        if ($bonusReportNumberSearch->agent_id !== '0')
            $criteria->compare('brn.agent_id', $bonusReportNumberSearch->agent_id);
        else
            $criteria->addCondition("brn.agent_id is null");

        $sql = "from bonus_report_number brn
            left outer join number n on (brn.number_id=n.id)
            left outer join agent a on (a.id=brn.agent_id)
            where " . $criteria->condition;

        $totalItemCount = Yii::app()->db->createCommand('select count(*) ' . $sql)->queryScalar($criteria->params);

        $dataProvider2 = new CSqlDataProvider('select a.*,n.*,brn.* ' . $sql, array(
            'totalItemCount' => $totalItemCount,
            'params' => $criteria->params,
            'sort' => array(
                'attributes' => array(
                    'number','personal_account','turnover','rate','sum','agent_id','status'
                ),
            ),
            'pagination' => array(
                'pageSize' => BonusReportNumber::ITEMS_PER_PAGE,
            ),
        ));

        $this->render('view', array(
            'model' => $bonusReportAgent,
            'dataProvider' => $dataProvider,
            'dataProvider2' => $dataProvider2,
            'bonusReportNumberSearch' => $bonusReportNumberSearch,
            'bonusReport' => BonusReport::model()->findByPk($id),
            'bonusReportAgent' => BonusReportAgent::model()->findByAttributes(array(
                'bonus_report_id' => $id,
                'agent_id' => loggedAgentId()
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
            'bra.agent_id=' . loggedAgentId() . ')';

        $this->render('list', array(
            'model' => $model,
            'dataProvider' => $dataProvider
        ));
    }

    private function error($msg)
    {
        Yii::app()->user->setFlash('error', $msg);
        $this->redirect(array());
    }

    private function errorInvalidFormat($msg)
    {
        $this->error(Yii::t('app', 'File has invalid format (%msg%)', array('%msg%' => $msg)));
    }

    private function roundSum($sum)
    {
        return floor($sum * 100 + 1e-6) / 100;
    }

    private function calculateBonuses($simBonus, $simAgent, $simIdNumberId, $comment, $operator_id)
    {

        $db = Yii::app()->db;

        // get agents info
        $agentsRaw = $db->createCommand(
            "select a.parent_id,a.id,arr.rate
            from agent a
            left outer join agent_referral_rate arr on (arr.agent_id=a.id and arr.operator_id=:operator_id)"
        )->queryAll(true, array(':operator_id' => $operator_id));

        $agents = array();
        foreach ($agentsRaw as $row) {
            $agents[$row['id']] = array('rate' => $row['rate'] / 100, 'parent_id' => $row['parent_id'] ? $row['parent_id'] : 0);
        }
        $agents[adminAgentId()]['payments'] = array(array('agent_id' => adminAgentId(), 'rate' => $agents[adminAgentId()]['rate']));

        unset($agentsRaw);

        // calculate agents payouts table
        do {
            $modified = false;
            foreach ($agents as $k => $v)
                if (!isset($v['payments']) && isset($agents[$v['parent_id']]['payments'])) {
                    $agents[$k]['payments'] = $agents[$v['parent_id']]['payments'];

                    $agents[$k]['payments'][] = array(
                        'agent_id' => $k,
                        'rate' => $v['rate']
                    );

                    // check rate validity
                    $idx = count($agents[$k]['payments']) - 1;
                    if ($agents[$k]['payments'][$idx]['rate'] > $agents[$k]['payments'][$idx - 1]['rate']) {
                        $agent1 = Agent::model()->findByPk($agents[$k]['payments'][$idx - 1]['agent_id']);
                        $agent2 = Agent::model()->findByPk($agents[$k]['payments'][$idx]['agent_id']);
                        $this->error(Yii::t('app', "Agent %agent1% has rate lower than subagent %agent2%", array('%agent1%' => $agent1, '%agent2%' => $agent2)));
                    }

                    $modified = true;
                }
        } while ($modified);

        $allAgentsIds = $db->createCommand("select id from agent")->queryColumn();
        $agentsBonuses = array();
        foreach ($allAgentsIds as $agentId)
            $agentsBonuses[$agentId] = array('sim_count' => 0, 'sum' => 0, 'sum_referrals' => 0);

        // store all info in database
        $trx = $db->beginTransaction();

        $bonusReport = new BonusReport;
        $bonusReport->dt = new EDateTime();
        $bonusReport->operator_id = $operator_id;
        $bonusReport->comment = $comment;
        $bonusReport->save();

        $bonusReportNumber=new BulkInsert('bonus_report_number',array('bonus_report_id','number_id','parent_agent_id','agent_id','turnover','rate','sum','status'));

        // calculate earned bonuses+statistics
        // keep in mind, that two different cards can have same personal_account or number
        // so, we must check for duplicates
        $processedSims=array();
        foreach ($simAgent as $v) {
            if ($processedSims[$v['sim_id']]) continue;
            $processedSims[$v['sim_id']]=true;

            $bonus = $this->roundSum($simBonus[$v['sim_id']]);

            $lastPaymentIndex = count($agents[$v['agent_id']]) - 1;
            foreach ($agents[$v['agent_id']]['payments'] as $i => $payment) {
                $agentsBonuses[$payment['agent_id']]['sim_count']++;
                $sum=$this->roundSum($bonus * $payment['rate']);
                $agentsBonuses[$payment['agent_id']]['sum'] += $sum;

                // if number is not null
                if ($simIdNumberId[$v['sim_id']]) {
                    $bonusReportNumber->insert(array(
                        'bonus_report_id'=>$bonusReport->id,
                        'number_id'=>$simIdNumberId[$v['sim_id']],
                        'parent_agent_id'=>$payment['agent_id'],
                        'agent_id'=>$i == $lastPaymentIndex ? null:$agents[$v['agent_id']]['payments'][$i + 1]['agent_id'],
                        'turnover'=>$bonus,
                        'rate'=>$payment['rate']*100,
                        'sum'=>$sum,
                        'status'=>$sum>1e-6? BonusReportNumber::STATUS_OK:BonusReportNumber::STATUS_TURNOVER_ZERO
                    ));
                } else {
                    $this->errorInvalidFormat(__LINE__." '{$v['sim_id']}'");
                }

                // for not last item we must substract provision of child agent
                $agentsBonuses[$payment['agent_id']]['sum_referrals'] += $i == $lastPaymentIndex ? 0 :
                    $this->roundSum($bonus * $agents[$v['agent_id']]['payments'][$i + 1]['rate']);
            }
        }

        $payment = new Payment();
        $payment->type = Payment::TYPE_BONUS;
        $payment->dt = new EDateTime();
        $payment->comment = Yii::t('app', 'Bonuses') . " '" . $comment . "'";

        $bonusReportAgent = new BonusReportAgent();
        $bonusReportAgent->bonus_report_id = $bonusReport->id;
        $bonusReportAgent->payment_id = $payment->id;

        foreach ($agentsBonuses as $agent_id => $data) {
            $sum = $this->roundSum($data['sum'] - $data['sum_referrals']);

            unset($bonusReportAgent->id);
            $bonusReportAgent->isNewRecord = true;


            if ($agents[$agent_id]['parent_id']==adminAgentId()) {
                Agent::deltaBalance($agent_id, $sum);

                unset($payment->id);
                $payment->isNewRecord = true;

                $payment->agent_id = $agent_id;
                $payment->sum = $this->roundSum($data['sum']);
                $payment->save();
                $bonusReportAgent->payment_id = $payment->id;
            }

            $bonusReportAgent->sim_count = $data['sim_count'];
            $bonusReportAgent->sum = $this->roundSum($data['sum']);
            $bonusReportAgent->sum_referrals = $this->roundSum($data['sum_referrals']);
            $bonusReportAgent->agent_id = $agent_id;
            $bonusReportAgent->save();
        }

        $bonusReportNumber->finish();

        // insert numbers, that missing in report
        $db->createCommand("
            insert into bonus_report_number (parent_agent_id,agent_id,number_id,bonus_report_id,turnover,sum,rate,status) (
                select s.parent_agent_id,s.agent_id,n.id as number_id,:bonus_report_id as bonus_report_id,NULL as turnover,NULL as sum,NULL as rate,IF(s.parent_agent_id!=1 && s.tariff_id=".Tariff::TARIFF_TERRITORY_ID.",'NO_PAYOUT','NUMBER_MISSING') as status
                from sim s
                join number n on (n.sim_id=s.parent_id)
                left outer join bonus_report_number brn on (brn.bonus_report_id=:bonus_report_id and brn.parent_agent_id=s.parent_agent_id and brn.number_id=n.id)
                where s.parent_agent_id is not null and s.operator_id=:operator_id and brn.id is null
            )
            ")->execute(array(
                ':operator_id'=>$operator_id,
                ':bonus_report_id'=>$bonusReport->id
            ));

        $trx->commit();

        $this->redirect(array('view', 'id' => $bonusReport->id));
    }

    private function processLoadBeeline($model, $reader, $file)
    {
        $reader->setReadFilter(new BeelineBonusReportReadFilter);

        try {
            $book = $reader->load($file->tempName);
        } catch (Exception $e) {
            $this->errorInvalidFormat($e->getMessage());
        }

        $sheet = $book->getActiveSheet();

        $rows = $sheet->getHighestRow();

        if ($sheet->getCellByColumnAndRow(3, 14)->getValue() != 'CTN')
            $this->errorInvalidFormat(__LINE__);
        if ($sheet->getCellByColumnAndRow(7, 14)->getValue() != 'Выручка без учета НДС, руб.')
            $this->errorInvalidFormat(__LINE__);

        $simBonus = array();
        $sum = 0;
        for ($row = 15; $row <= $rows; $row++) {
            $ctn = trim($sheet->getCellByColumnAndRow(3, $row)->getValue());
            $bonus = $sheet->getCellByColumnAndRow(7, $row)->getValue();

            $sum += $bonus;

            if ($ctn == '') continue;
            if (!preg_match('%^\d{10}$%', $ctn)) $this->errorInvalidFormat(__LINE__ . " $row '$ctn'");

            $simBonus[$ctn] = $bonus;
        }

        $book->disconnectWorksheets();
        unset($book);

        if ($sum == 0) $this->errorInvalidFormat(__LINE__);

        $db = Yii::app()->db;

        $personal_accounts = array();
        foreach ($simBonus as $k => $v) $numbers[] = $db->quoteValue($k);

        // get agents, to which bonused sims was sent
        $simAgent = $db->createCommand("select number as sim_id,parent_agent_id as agent_id
            from sim
            where operator_id=" . Operator::OPERATOR_BEELINE_ID .
            " and parent_agent_id is not null and agent_id is null and number in (" .
            implode(',', $numbers) . ") order by sim.id desc")->queryAll();

        // get number->number_id mapping
        $rawNumbers=Yii::app()->db->createCommand("
            select n.id,n.number
            from number n
            join sim s on (s.id=n.sim_id and s.operator_id=:operator_id)
        ")->queryAll(true,array(':operator_id'=>Operator::OPERATOR_BEELINE_ID));

        $simIdNumberId=array();
        foreach($rawNumbers as $row)
            $simIdNumberId[$row['number']]=$row['id'];
        unset($rawNumbers);

        $this->calculateBonuses($simBonus, $simAgent, $simIdNumberId, $model->comment, Operator::OPERATOR_BEELINE_ID);
    }

    private function processLoadMegafon($model, $reader, $file)
    {
        $reader->setReadFilter(new MegafonBonusReportReadFilter);

        try {
            $book = $reader->load($file->tempName);
        } catch (Exception $e) {
            $this->errorInvalidFormat($e->getMessage());
        }

        $sheet = $book->getSheet(0);

        $rows = $sheet->getHighestRow();

        if ($sheet->getCellByColumnAndRow(1, 12)->getValue() != 'ACCOUNT')
            $this->errorInvalidFormat(__LINE__);
        if ($sheet->getCellByColumnAndRow(9, 12)->getValue() != 'Сумма счета, руб. без НДС')
            $this->errorInvalidFormat(__LINE__);

        $simBonus = array();
        $sum = 0;
        for ($row = 13; $row <= $rows; $row++) {
            $ctn = trim($sheet->getCellByColumnAndRow(1, $row)->getValue());
            $bonus = $sheet->getCellByColumnAndRow(9, $row)->getValue();

            $sum += $bonus;

            if ($ctn == '') continue;
            if ($ctn == 'Итого:') break;
            if (!preg_match('%^\d{7,8}$%', $ctn)) $this->errorInvalidFormat(__LINE__ . " $row '$ctn'");

            $simBonus[$ctn] = $bonus;
        }

        $book->disconnectWorksheets();
        unset($book);

        if ($sum == 0) $this->errorInvalidFormat(__LINE__);

        $db = Yii::app()->db;

        $personal_accounts = array();
        foreach ($simBonus as $k => $v) $personal_accounts[] = $db->quoteValue($k);

        // get agents, to which bonused sims was sent
        $simAgent = $db->createCommand("select personal_account as sim_id,IF(tariff_id=".Tariff::TARIFF_TERRITORY_ID.",1,parent_agent_id) as agent_id
            from sim
            where operator_id=" . Operator::OPERATOR_MEGAFON_ID . " and agent_id is null and personal_account in (" .
            implode(',', $personal_accounts) . ")".
            " and tariff_id!=".Tariff::TARIFF_TERRITORY_ID.
            " order by sim.id desc")->queryAll();

        // get personal_id->number_id mapping
        $rawNumbers=Yii::app()->db->createCommand("
            select n.id,n.personal_account
            from number n
            join sim s on (s.id=n.sim_id and s.operator_id=:operator_id)
        ")->queryAll(true,array(':operator_id'=>Operator::OPERATOR_MEGAFON_ID));
        $simIdNumberId=array();
        foreach($rawNumbers as $row)
            $simIdNumberId[$row['personal_account']]=$row['id'];
        unset($rawNumbers);

        $this->calculateBonuses($simBonus, $simAgent, $simIdNumberId, $model->comment, Operator::OPERATOR_MEGAFON_ID);
    }

    private function processLoad($model)
    {
        $file = CUploadedFile::getInstance($model, 'file');

        if ($file === null) {
            Yii::app()->user->setFlash('error', Yii::t('app', 'File uploaded with error'));
        }

        Yii::import('application.vendors.PHPExcel', true);

        try {
            $reader = PHPExcel_IOFactory::createReader(PHPExcel_IOFactory::identify($file->tempName));
        } catch (Exception $e) {
            $this->errorInvalidFormat($e->getMessage());
        }

        // csv doesn't have setReadDataOnly method
        if (method_exists($reader, 'setReadDataOnly')) $reader->setReadDataOnly(true);

        switch ($model->operator) {
            case Operator::OPERATOR_BEELINE_ID:
                $this->processLoadBeeline($model, $reader, $file);
                break;
            case Operator::OPERATOR_MEGAFON_ID:
                $this->processLoadMegafon($model, $reader, $file);
                break;
            default:
                Yii::app()->user->setFlash('error', Yii::t('app', 'Loading bonuses for this operator is not yet implemented'));
                $this->redirect(array());
        }

        Yii::app()->user->setFlash('success', Yii::t('app', 'Loading bonuses completed successfully'));
        $this->redirect(array(''));
    }

    public function actionLoad()
    {
        $model = new LoadBonusReport();

        $this->performAjaxValidation($model);

        if (isset($_POST['LoadBonusReport'])) {
            $model->setAttributes($_POST['LoadBonusReport']);

            if ($model->validate()) {
                $this->processLoad($model);
            }
        }

        $this->render('load', array(
            'model' => $model
        ));
    }
}
