<?php

class BalanceReportController extends BaseGxController
{
    public function filters() {
        return array_merge(parent::filters(),array(
            array('LoggingFilter +load')
        ));
    }

    public function actionDelete($id)
    {
        if (Yii::app()->getRequest()->getIsPostRequest() && isAdmin()) {
            $trx = Yii::app()->db->beginTransaction();

            BalanceReportNumber::model()->deleteAllByAttributes(array('balance_report_id'=>$id));
            BalanceReport::model()->deleteByPk($id);

            $trx->commit();

            if (!Yii::app()->getRequest()->getIsAjaxRequest())
                $this->redirect(array('list'));
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    public function actionView($id)
    {
        $model = new BalanceReportNumberSearch();
        $model->unsetAttributes();

        if (isset($_GET['BalanceReportNumberSearch']))
            $model->setAttributes($_GET['BalanceReportNumberSearch']);

        $criteria=new CDbCriteria();
        $criteria->compare('n.personal_account',$model->personal_account);
        $criteria->compare('n.number',$model->number);
        $criteria->compare('brn.balance',$model->balance);
        $criteria->compare('brn.balance_report_id',$id);

        $sql="from balance_report_number brn
            join number n on (n.id=brn.number_id)
            where ".$criteria->condition;

        $totalItemCount=Yii::app()->db->createCommand('select count(*) '.$sql)->queryScalar($criteria->params);

        $dataProvider=new CSqlDataProvider('select * '.$sql,array(
            'totalItemCount'=>$totalItemCount,
            'params'=>$criteria->params,
            'sort'=>array(
                'attributes'=>array(
                    'personal_account',
                    'number',
                    'balance',
                ),
            ),
            'pagination'=>array(
                'pageSize'=>BalanceReportNumber::ITEMS_PER_PAGE,
            ),
        ));

        $this->render('view', array(
            'model' => $model,
            'balanceReport' => BalanceReport::model()->findByPk($id),
            'balanceReportNumberSearch' =>$model,
            'balanceReportNumberDataProvider'=>$dataProvider
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

    private function error($msg) {
        Yii::app()->user->setFlash('error',$msg);
        $this->redirect(array());
    }

    private function errorInvalidFormat($msg) {
        $this->error(Yii::t('app','File has invalid format (%msg%)',array('%msg%'=>$msg)));
    }

    private function loadBalances($numberBalances,$comment,$operator_id) {
        $db=Yii::app()->db;

        // store all info in database
        $trx=$db->beginTransaction();

        $balanceReport=new BalanceReport;
        $balanceReport->dt=new EDateTime();
        $balanceReport->operator_id=$operator_id;
        $balanceReport->comment=$comment;
        $balanceReport->save();

        $balanceReportNumberBulkInsert=new BulkInsert('balance_report_number',array('number_id','balance_report_id','balance'));

        $cmdFindNumberId=Yii::app()->db->createCommand("select id from number where personal_account=:personal_account and number=:number");

        foreach($numberBalances as $numberBalance) {

            $numberId=$cmdFindNumberId->queryScalar(array(
                ':personal_account'=>$numberBalance['personal_account'],
                ':number'=>$numberBalance['number'],
            ));

            // create number, if it was not found
            if (!$numberId) {
                $number=new Number();
                $number->number=$numberBalance['number'];
                $number->personal_account=$numberBalance['personal_account'];
                $number->status=Number::STATUS_NORMAL;
                $number->save();
                $numberId=$number->id;

                // search number in sim cards
                $sim=Sim::model()->findByAttributes(array(
                    'parent_agent_id'=>adminAgentId(),
                    'personal_account'=>$numberBalance['personal_account'],
                    'number'=>$numberBalance['number']
                ));

                // create sim, if it was not found
                if (!$sim) {
                    $sim=new Sim();
                    $sim->parent_agent_id=adminAgentId();
                    $sim->personal_account=$numberBalance['personal_account'];
                    $sim->number=$numberBalance['number'];
                    $sim->operator_id=$operator_id;
                    $sim->save();
                    $sim->parent_id=$sim->id;
                    $sim->save();

                }
            }

            $balanceReportNumberBulkInsert->insert(array(
                'number_id'=>$numberId,
                'balance_report_id'=>$balanceReport->id,
                'balance'=>$numberBalance['balance']
            ));
        }

        $balanceReportNumberBulkInsert->finish();

        $trx->commit();

        $this->redirect(array('view','id'=>$balanceReport->id));
    }

    private function processLoadMegafon($model,$reader,$file) {
        $reader->setReadFilter(new MegafonBalanceReportReadFilter);

        try {
            $book = $reader->load($file->tempName);
        } catch (Exception $e) {
            $this->errorInvalidFormat($e->getMessage());
        }

        $sheet = $book->getActiveSheet();

        $rows=$sheet->getHighestRow();

        $balances=array();
        for($row=4;$row<=$rows;$row++) {
            $personal_account=$sheet->getCellByColumnAndRow(0, $row)->getValue();
            $number=$sheet->getCellByColumnAndRow(5, $row)->getValue();
            $balance=$sheet->getCellByColumnAndRow(12, $row)->getValue();

            if ($personal_account=='' || $number=='') continue;
            if (!preg_match('/^\d{7,8}$/',$personal_account)) {$this->errorInvalidFormat(__LINE__)." $row '$personal_account'";}
            if ($number!='') {
                if (!preg_match('/^\d+[^0-9]*- (\d{10}),?\s*$/',$number,$m)) $this->errorInvalidFormat(__LINE__." $row '$number'");
                $number=$m[1];
            }
            if ($balance==='') $this->errorInvalidFormat(__LINE__." $row");

            $balances[]=array(
                'personal_account'=>$personal_account,
                'number'=>$number,
                'balance'=>floatval($balance)
            );
        }

        $book->disconnectWorksheets();
        unset($book);

        $this->loadBalances($balances,$model->comment,Operator::OPERATOR_MEGAFON_ID);
    }

    private function processLoad($model) {
        $file = CUploadedFile::getInstance($model, 'file');

        if ($file === null) {
            Yii::app()->user->setFlash('error',Yii::t('app','File uploaded with error'));
        }

        Yii::import('application.vendors.PHPExcel', true);

        try {
            $reader=PHPExcel_IOFactory::createReader(PHPExcel_IOFactory::identify($file->tempName));
        } catch (Exception $e) {
            $this->errorInvalidFormat($e->getMessage());
        }

        // csv doesn't have setReadDataOnly method
        if (method_exists( $reader,'setReadDataOnly')) $reader->setReadDataOnly(true);

        switch ($model->operator) {
            case Operator::OPERATOR_MEGAFON_ID:
                $this->processLoadMegafon($model,$reader,$file);
                break;
            default:
                Yii::app()->user->setFlash('error',Yii::t('app','Loading balance for this operator is not yet implemented'));
                $this->redirect(array());
        }

        Yii::app()->user->setFlash('success',Yii::t('app','Loading balance report completed successfully'));
        $this->redirect(array(''));
    }

    public function actionLoad() {
        $model=new LoadBalanceReport();

        $this->performAjaxValidation($model);

        if (isset($_POST['LoadBalanceReport'])) {
            $model->setAttributes($_POST['LoadBalanceReport']);

            if ($model->validate()) {
                $this->processLoad($model);
            }
        }

        $this->render('load',array(
            'model'=>$model
        ));
    }
}
