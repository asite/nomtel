<?php

class DeliveryReportController extends BaseGxController
{
    public function checkDeliveryReportPermissions($deliveryReport) {
        if (!Yii::app()->user->getState('isAdmin') &&
            Yii::app()->user->getState('isAdmin')!=$deliveryReport->agent_id) $this->redirect(array('list'));
    }

    public function actionList()
    {
        $model = new DeliveryReport('search');
        $model->unsetAttributes();

        if (isset($_GET['DeliveryReport']))
            $model->setAttributes($_GET['DeliveryReport']);

        if (!Yii::app()->user->getState('isAdmin'))
            $model->agent_id=Yii::app()->user->getState('agentId');

            $this->render('list', array(
                'model' => $model,
            ));
    }

    public function actionView($id)
    {
        $model = $this->loadModel($id, 'DeliveryReport');
        $this->checkDeliveryReportPermissions($model);

        $sim = new Sim('search');
        $sim->unsetAttributes();

        if (isset($_GET['Sim']))
            $sim->setAttributes($_GET['Sim']);
        $sim->delivery_report_id = $id;

        $this->render('view', array(
            'model' => $model,
            'sim' => $sim
        ));
    }

}