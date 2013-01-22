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
        $dataProvider->criteria->order = "dt DESC";

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

    public function actionDelete($id) {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            try {
                $trx = Yii::app()->db->beginTransaction();
                $act = $this->loadModel($id, 'Act');

                if (!Yii::app()->user->checkAccess('deleteAct',array('act'=>$act)))
                    $this->ajaxError(Yii::t('app','You are not authorized to perform this action.'));

                if ($act->type==Act::TYPE_SIM) {
                    $passedSims=Sim::model()->count(array(
                        'condition'=>'parent_act_id=:act_id and agent_id is not null',
                        'params'=>array(
                            ':act_id'=>$act->id
                        )
                    ));

                    if ($passedSims>0) $this->ajaxError(Yii::t("app", "Can't revert this act: Some of sim agent passed to subagents"));

                    $connectedSims=Yii::app()->db->createCommand("
                        select count(*) from sim
                        join number on (number.number=sim.number and number.status!=:STATUS_FREE)
                        where sim.act_id=:act_id
                        ")->queryScalar(array(':act_id'=>$act->id,'STATUS_FREE'=>Number::STATUS_FREE));

                    if ($connectedSims>0) $this->ajaxError(Yii::t("app", "Can't revert this act: Some of numbers are non free"));

                    // modify agent sim records
                    Sim::model()->updateAll(array(
                            'agent_id'=>new CDbExpression('NULL'),
                            'act_id'=>new CDbExpression('NULL')
                        ),
                        'act_id=:act_id',
                        array(
                            ':act_id'=>$act->id
                        ));

                    // delete subagent sim records
                    Sim::model()->deleteAll('parent_act_id=:parent_act_id',array(':parent_act_id'=>$act->id));
                }

                $act->delete();

                // recalc agent statistics
                $agent=$act->agent;
                $agent->recalcAllStats();
                $agent->save();

                $trx->commit();
            } catch (CDbException $e) {
                $this->ajaxError(Yii::t("app", "Can't delete this Act because it is used in system"));
            }

            if (!Yii::app()->getRequest()->getIsAjaxRequest())
                $this->redirect(array('admin'));
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
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