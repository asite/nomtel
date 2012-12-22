<?php

class BonusReportController extends BaseGxController
{

    public function actionDelete($id)
    {
        if (Yii::app()->getRequest()->getIsPostRequest() && isAdmin()) {
            $trx = Yii::app()->db->beginTransaction();

            $bonusReports = Yii::app()->db->createCommand("select agent_id,sum from payment where bonus_report_id=:bonus_report_id")->
                queryAll(true, array(':bonus_report_id' => $id));

            $cmd = Yii::app()->db->createCommand("update agent set balance=balance-:sum where id=:id");
            foreach ($bonusReports as $report)
                $cmd->execute(array(':id' => $report['agent_id'], ':sum' => $report['sum']));

            Payment::model()->deleteAllByAttributes(array('bonus_report_id' => $id));
            BonusReport::model()->deleteByPk($id);

            $trx->commit();

            if (!Yii::app()->getRequest()->getIsAjaxRequest())
                $this->redirect(array('list'));
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    public function actionView($id)
    {
        $model = new Payment('list');
        $model->unsetAttributes();

        if (isset($_GET['Payment']))
            $model->setAttributes($_GET['Payment']);

        $dataProvider = $model->search();

        $dataProvider->criteria->alias = 'payment';
        $dataProvider->criteria->join = 'join bonus_report on (bonus_report.id=payment.bonus_report_id and bonus_report_id='.
            Yii::app()->db->quoteValue($id).') '.
            'join agent on (agent.id=payment.agent_id and agent.parent_id='.loggedAgentId().')';

        $this->render('view', array(
            'model' => $model,
            'dataProvider' => $dataProvider,
            'parentModel' => BonusReport::model()->findByPk($id)
        ));

    }

    public function actionList()
    {
        $model = new BonusReport('list');
        $model->unsetAttributes();

        if (isset($_GET['BonusReport']))
            $model->setAttributes($_GET['BonusReport']);

        $dataProvider = $model->search();

        if (!isAdmin()) {
            $dataProvider->criteria->alias = 'bonus_report';
            $dataProvider->criteria->join = 'join payment on (payment.bonus_report_id=bonus_report.id and ' .
                'payment.agent_id='.loggedAgentId(). ')';
        }

        $this->render('list', array(
            'model' => $model,
            'dataProvider' => $dataProvider
        ));
    }
}
