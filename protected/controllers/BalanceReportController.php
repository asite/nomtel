<?php

class BalanceReportController extends BaseGxController
{
    public function filters()
    {
        return array_merge(parent::filters(), array(
            array('LoggingFilter +load')
        ));
    }

    public function actionDelete($id)
    {
        if (Yii::app()->getRequest()->getIsPostRequest() && isAdmin()) {
            $trx = Yii::app()->db->beginTransaction();

            BalanceReportNumber::model()->deleteAllByAttributes(array('balance_report_id' => $id));
            BalanceReport::model()->deleteByPk($id);

            $this->recalcWarnings();

            $trx->commit();

            if (!Yii::app()->getRequest()->getIsAjaxRequest())
                $this->redirect(array('list'));
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }


    private function recalcOperatorWarnings($reportId1, $reportId2, $reportId3, $warnOnEqualValues=true)
    {
        $goodIds = array();
        $warnedIds = array();

        $reader = Yii::app()->db->createCommand("
            select n.id,brn1.balance as b1,brn2.balance as b2,brn3.balance as b3
            from (
              select distinct number_id as id
              from balance_report_number
              where balance_report_id in (:report_id1,:report_id2,:report_id3)
            ) as n
            left outer join balance_report_number brn1 on (brn1.balance_report_id=:report_id1 and brn1.number_id=n.id)
            left outer join balance_report_number brn2 on (brn2.balance_report_id=:report_id2 and brn2.number_id=n.id)
            left outer join balance_report_number brn3 on (brn3.balance_report_id=:report_id3 and brn3.number_id=n.id)
        ")->query(array(
            ':report_id1' => $reportId1,
            ':report_id2' => $reportId2,
            ':report_id3' => $reportId3,
        ));

        foreach ($reader as $row) {
            $b = array($row['b1'], $row['b2'], $row['b3']);

            $warned = false;

            // if number not present in current or previous report
            if (($b[1] == '' && $b[2]!='') || $b[2] == '') $warned = true;

            if ($warnOnEqualValues) {
                // if balance of last three reports is equal
                if ($b[0] == $b[1] && $b[1] == $b[2]) $warned = true;
            }

            // if balance of last three reports is zero or below zero
            if ($b[0]<1e-6 && $b[1]<1e-6 && $b[2]<1e-6) $warned = true;

            if ($warned) $warnedIds[] = $row['id']; else $goodIds[] = $row['id'];
        }

        if (!empty($goodIds)) {
            Yii::app()->db->createCommand('update number set status=:status_normal, warning_dt=NULL where id in (' .
                implode(',', $goodIds) . ') and status=:status_warning')->execute(array(
                ':status_normal' => Number::STATUS_NORMAL,
                ':status_warning' => Number::STATUS_WARNING,
            ));
        }

        if (!empty($warnedIds)) {
            Yii::app()->db->createCommand('update number set status=:status_warning, warning_dt=NOW() where id in (' .
                implode(',', $warnedIds) . ') and status=:status_normal')->execute(array(
                ':status_normal' => Number::STATUS_NORMAL,
                ':status_warning' => Number::STATUS_WARNING,
            ));
        }
    }

    private function recalcWarnings()
    {
        $operators = Operator::model()->findAll();

        foreach ($operators as $operator) {
            $balanceReports = BalanceReport::model()->findAll(array(
                'condition' => 'operator_id=:operator_id',
                'params' => array('operator_id' => $operator->id),
                'limit' => 3,
                'order' => 'id desc'
            ));

            if (count($balanceReports) < 3) continue;

            $this->recalcOperatorWarnings(
                $balanceReports[2]->id,
                $balanceReports[1]->id,
                $balanceReports[0]->id,
                $operator->id=Operator::OPERATOR_MEGAFON_ID
            );
        }
    }

    public function actionView($id)
    {
        $model = new BalanceReportNumberSearch();
        $model->unsetAttributes();

        if (isset($_GET['BalanceReportNumberSearch']))
            $model->setAttributes($_GET['BalanceReportNumberSearch']);

        $criteria = new CDbCriteria();
        $criteria->compare('n.personal_account', $model->personal_account);
        $criteria->compare('n.number', $model->number);
        $criteria->compare('brn.balance', $model->balance);
        $criteria->compare('brn.balance_report_id', $id);

        $sql = "from balance_report_number brn
            join number n on (n.id=brn.number_id)
            where " . $criteria->condition;

        $totalItemCount = Yii::app()->db->createCommand('select count(*) ' . $sql)->queryScalar($criteria->params);

        $dataProvider = new CSqlDataProvider('select * ' . $sql, array(
            'totalItemCount' => $totalItemCount,
            'params' => $criteria->params,
            'sort' => array(
                'attributes' => array(
                    'personal_account',
                    'number',
                    'balance',
                ),
            ),
            'pagination' => array(
                'pageSize' => BalanceReportNumber::ITEMS_PER_PAGE,
            ),
        ));

        $this->render('view', array(
            'model' => $model,
            'balanceReport' => BalanceReport::model()->findByPk($id),
            'balanceReportNumberSearch' => $model,
            'balanceReportNumberDataProvider' => $dataProvider
        ));
    }

    public function actionList()
    {
        $model = new BalanceReport('list');
        $model->unsetAttributes();

        if (isset($_GET['BalanceReport']))
            $model->setAttributes($_GET['BalanceReport']);

        $this->render('list', array(
            'model' => $model,
            'dataProvider' => $model->search()
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

    private function loadBalances($numberBalances, $comment, $operator_id)
    {
        $db = Yii::app()->db;

        // store all info in database
        $trx = $db->beginTransaction();

        $balanceReport = new BalanceReport;
        $balanceReport->dt = new EDateTime();
        $balanceReport->operator_id = $operator_id;
        $balanceReport->comment = $comment;
        $balanceReport->save();

        $balanceReportNumberBulkInsert = new BulkInsert('balance_report_number', array('number_id', 'balance_report_id', 'balance'));

        $cmdFindNumberId = Yii::app()->db->createCommand("select id from number where number=:number");

        foreach ($numberBalances as $numberBalance) {

            $numberId = $cmdFindNumberId->queryScalar(array(
                ':number' => $numberBalance['number'],
            ));

            // create number, if it was not found
            if (!$numberId) {
                $number = new Number();
                $number->number = $numberBalance['number'];
                $number->personal_account = $numberBalance['personal_account'];
                $number->status = Number::STATUS_NORMAL;
                $number->save();
                $numberId = $number->id;

                // search number in sim cards (attached to admin or not yet received)
                $sim = Sim::model()->find(array(
                    'condition'=>"(parent_agent_id is null or parent_agent_id=:admin_agent_id) and
                        personal_account=:personal_account and number=:number",
                    'params' => array(
                        ':admin_agent_id'=>adminAgentId(),
                        ':personal_account'=>$numberBalance['personal_account'],
                        ':number'=>$numberBalance['number']
                    )
                ));

                // attach not yet received sim to admin
                if ($sim && $sim->parent_agent_id!=adminAgentId()) {
                    $sim->parent_agent_id=adminAgentId();
                    $sim->save();
                }

                // create sim, if it was not found
                if (!$sim) {
                    $sim = new Sim();
                    $sim->parent_agent_id = adminAgentId();
                    $sim->personal_account = $numberBalance['personal_account'];
                    $sim->number = $numberBalance['number'];
                    $sim->operator_id = $operator_id;
                    $sim->save();
                    $sim->parent_id = $sim->id;
                    $sim->save();

                }
            }

            $balanceReportNumberBulkInsert->insert(array(
                'number_id' => $numberId,
                'balance_report_id' => $balanceReport->id,
                'balance' => $numberBalance['balance']
            ));
        }

        $balanceReportNumberBulkInsert->finish();

        $this->recalcWarnings();

        $trx->commit();

        $this->redirect(array('view', 'id' => $balanceReport->id));
    }

    private function processLoadBeeline($model, $reader, $file)
    {
        $reader->setReadFilter(new BeelineBalanceReportReadFilter);

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

        $balances = array();
        for ($row = 15; $row <= $rows; $row++) {
            $number = trim($sheet->getCellByColumnAndRow(3, $row)->getValue());
            $balance = $sheet->getCellByColumnAndRow(7, $row)->getValue();

            if ($number == '') continue;

            if (isset($numbers[$number])) {
                echo $number;
                exit;
            }

            if (!preg_match('/^\d{10}$/', $number)) $this->errorInvalidFormat(__LINE__ . " $row '$number'");
            if ($balance === '') $this->errorInvalidFormat(__LINE__ . " $row");

            // in beeline report one number can have many rows, don't know why...
            if (!$balances[$number]) {
                $balances[$number] = array(
                    'personal_account' => '',
                    'number' => $number,
                    'balance' => floatval($balance)
                );
            } else {
                $balances[$number]['balance']+=floatval($balance);
            }
        }

        $book->disconnectWorksheets();
        unset($book);

        $this->loadBalances($balances, $model->comment, Operator::OPERATOR_BEELINE_ID);
    }

    private function processLoadMegafon($model, $reader, $file)
    {
        $reader->setReadFilter(new MegafonBalanceReportReadFilter);

        try {
            $book = $reader->load($file->tempName);
        } catch (Exception $e) {
            $this->errorInvalidFormat($e->getMessage());
        }

        $sheet = $book->getActiveSheet();

        $rows = $sheet->getHighestRow();

        $balances = array();
        for ($row = 4; $row <= $rows; $row++) {
            $personal_account = trim($sheet->getCellByColumnAndRow(0, $row)->getValue());
            $number = trim($sheet->getCellByColumnAndRow(5, $row)->getValue());
            $balance = $sheet->getCellByColumnAndRow(12, $row)->getValue();

            if ($personal_account == '' || $number == '') continue;
            if (!preg_match('/^\d{7,8}$/', $personal_account)) {
                $this->errorInvalidFormat(__LINE__) . " $row '$personal_account'";
            }
            if ($number != '') {
                if (!preg_match('/^\d+[^0-9]*- (\d{10}),?\s*$/', $number, $m)) $this->errorInvalidFormat(__LINE__ . " $row '$number'");
                $number = $m[1];
            }
            if ($balance === '') $this->errorInvalidFormat(__LINE__ . " $row");

            $balances[] = array(
                'personal_account' => $personal_account,
                'number' => $number,
                'balance' => floatval($balance)
            );
        }

        $book->disconnectWorksheets();
        unset($book);

        $this->loadBalances($balances, $model->comment, Operator::OPERATOR_MEGAFON_ID);
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
                Yii::app()->user->setFlash('error', Yii::t('app', 'Loading balance for this operator is not yet implemented'));
                $this->redirect(array());
        }

        Yii::app()->user->setFlash('success', Yii::t('app', 'Loading balance report completed successfully'));
        $this->redirect(array(''));
    }

    public function actionLoad()
    {
        $model = new LoadBalanceReport();

        $this->performAjaxValidation($model);

        if (isset($_POST['LoadBalanceReport'])) {
            $model->setAttributes($_POST['LoadBalanceReport']);

            if ($model->validate()) {
                $this->processLoad($model);
            }
        }

        $this->render('load', array(
            'model' => $model
        ));
    }
}
