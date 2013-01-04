<?php

class ActController extends BaseGxController
{
    public function additionalAccessRules()
    {
        return array(
            array('allow', 'actions' => array('list', 'view', 'report'), 'roles' => array('agent')),
        );
    }

    public function actionList()
    {
        $model = new Act('search');
        $model->unsetAttributes();

        if (isset($_GET['Act']))
            $model->setAttributes($_GET['Act']);

        $dataProvider = $model->search();
        $dataProvider->criteria->alias = 'act';
        $dataProvider->criteria->join = "join agent on (agent.id=act.agent_id and agent.parent_id=" . loggedAgentId() . ')';

        $this->render('list', array(
            'model' => $model,
            'dataProvider' => $dataProvider
        ));
    }

    public function actionView($id)
    {
        $model = $this->loadModel($id, 'Act');
        if (loggedAgentId() != $model->agent_id &&
            loggedAgentId() != $model->agent->parent_id
        )
            throw new CHttpException(400, Yii::t('giix', 'Your request is invalid.'));

        $sim = new Sim('search');
        $sim->unsetAttributes();

        if (isset($_GET['Sim']))
            $sim->setAttributes($_GET['Sim']);
        $sim->act_id = $id;

        $this->render('view', array(
            'model' => $model,
            'sim' => $sim
        ));
    }

    public function actionReport($id)
    {
        $model = $this->loadModel($id, 'Sim');

        if (loggedAgentId() != $model->parent_agent_id)
            throw new CHttpException(400, Yii::t('giix', 'Your request is invalid.'));

        $report = new SimReport();
        $this->performAjaxValidation($report);

        if (isset($_POST['SimReport'])) {
            $report->setAttributes($_POST['SimReport']);

            if ($report->validate()) {
                $body = $this->renderPartial('mailReport', array(
                    'model' => $model,
                    'report' => $report
                ), true);

                $mail = new YiiMailMessage();
                $mail->setSubject(Yii::t('app', 'Problem with sim card'));
                $mail->setFrom(Yii::app()->params['adminEmailFrom']);
                $mail->setTo(Yii::app()->params['adminEmail']);
                $mail->setBody($body);

                if (Yii::app()->mail->send($mail))
                    Yii::app()->user->setFlash('success', Yii::t('app', 'Problem report sent to admin'));
                else
                    Yii::app()->user->setFlash('error', Yii::t('app', 'Problem sending email'));

                $this->redirect(array('view', 'id' => $model->parent_act_id));
            }
        }

        $this->render('report', array(
            'model' => $model,
            'report' => $report
        ));
    }
}