<?php

class DeliveryReportController extends BaseGxController
{
    public function actionList()
    {
        $model = new DeliveryReport('search');
        $model->unsetAttributes();

        if (isset($_GET['DeliveryReport']))
            $model->setAttributes($_GET['DeliveryReport']);

        $dataProvider=$model->search();
        if (Yii::app()->user->getState('isAdmin'))
            $dataProvider->criteria->addCondition("parent_agent_id is null");
        else
            $dataProvider->criteria->addColumnCondition(array("parent_agent_id"=>Yii::app()->user->getState('agentId')));

        $this->render('list', array(
            'model' => $model,
            'dataProvider' => $dataProvider
        ));
    }

    public function actionView($id)
    {
        $model = $this->loadModel($id, 'DeliveryReport');
        if (Yii::app()->user->getState('agentId')!=$model->parent_agent_id &&
            Yii::app()->user->getState('agentId')!=$model->agent_id)
            throw new CHttpException(400, Yii::t('giix', 'Your request is invalid.'));

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

        if (Yii::app()->user->getState('agentId')!=$model->parent_agent_id)
            throw new CHttpException(400, Yii::t('giix', 'Your request is invalid.'));

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