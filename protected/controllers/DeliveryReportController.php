<?php

class DeliveryReportController extends BaseGxController
{
    public function checkDeliveryReportPermissions($deliveryReport) {
        if (!Yii::app()->user->getState('isAdmin') &&
            Yii::app()->user->getState('agentId')!=$deliveryReport->agent_id) $this->redirect(array('list'));
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

    public function actionReport($id) {
        $model = $this->loadModel($id, 'Sim');
        $this->checkDeliveryReportPermissions($model->deliveryReport);

        $report=new SimReport();
        $this->performAjaxValidation($report);

        if (isset($_POST['SimReport'])) {
            $report->setAttributes($_POST['SimReport']);

            if ($report->validate()) {
                $body=$this->renderPartial('mailReport',array(
                    'model' => $model,
                    'report' => $report
                ),true);

                $mail=new YiiMailMessage();
		$mail->setSubject(Yii::t('app','Problem with sim card'));
		$mail->setFrom(Yii::app()->params['adminEmailFrom']);
		$mail->setTo(Yii::app()->params['adminEmail']);
		$mail->setBody($body);

		if (Yii::app()->mail->send($mail))
            	    Yii::app()->user->setFlash('success',Yii::t('app','Problem report sent to admin'));
		else
            	    Yii::app()->user->setFlash('error',Yii::t('app','Problem sending email'));

                $this->redirect(array('view','id'=>$model->delivery_report_id));
            }
        }

        $this->render('report', array(
            'model' => $model,
            'report' => $report
        ));
    }
}