<?php

class ActController extends BaseGxController
{
    public function additionalAccessRules()
    {
        return array(
            array('allow', 'actions' => array('list', 'view', 'report','delete','fromParent'), 'roles' => array('agent')),
        );
    }

    public function actionFromParent()
    {
        if (!isFlag('is_making_parent_invoices')) throw new CHttpException(403);

        if ($_POST['ICCtoSelect'] != '') {
            $id_arr = explode("\n", $_POST['ICCtoSelect']);

            $agent=Agent::model()->findByPk(loggedAgentId());

            if (!empty($id_arr)) {
                $quotedIds=array();
                foreach($id_arr as $id) $quotedIds[]=Yii::app()->db->quoteValue(trim($id));
                $in='('.implode(',',$quotedIds).')';
                $sql = "select id from sim where (number in $in or icc in $in) and agent_id is NULL and parent_agent_id='".$agent->parent_id."' and is_active=1";
                $sims=Yii::app()->db->createCommand($sql)->queryAll();
            } else {
                $sims=array();
            }

            $ids = array();
            foreach ($sims as $value) {
                $ids[$value['id']]=$value['id'];
            }

            if (count($ids) > 0) {
                $sessionData=new SessionData('SimController');
                $key=$sessionData->add($ids);

                $this->redirect(array('sim/move', 'key' => $key,'Act[agent_id]'=>loggedAgentId()));
            } else
                Yii::app()->user->setFlash('error', '<strong>Ошибка</strong> не найдено сим в базе.');
        }

        $this->render('fromParent');
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
                        join number on (number.number=sim.number and number.status!=:STATUS_FREE  and number.status!=:STATUS_UNKNOWN)
                        where sim.act_id=:act_id
                        ")->queryScalar(array(':act_id'=>$act->id,'STATUS_FREE'=>Number::STATUS_FREE,'STATUS_UNKNOWN'=>Number::STATUS_UNKNOWN));

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

                NumberHistory::deleteByApproximateDtAndSubstringOrModel($act->dt,$act);
                $trx->commit();
            } catch (CDbException $e) {
                $this->ajaxError($e);
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

    public function actionPrint($id) {
        $this->layout = 'simply';

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

        $this->render('print', array(
            'model' => $model,
            'sim' => $sim
        ));
    }
}