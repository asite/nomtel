<?php

class AgentController extends BaseGxController
{
    public function checkAgentPermissions($agent) {
        if (!Yii::app()->user->getState('isAdmin') &&
            Yii::app()->user->getState('agentId')!=$agent->parent_id) $this->redirect(array('list'));
    }

    private function save($model,$user,$referralRates) {
        $trx=Yii::app()->db->beginTransaction();

        $user->save();

        $model->user_id=$user->id;
        $model->save();

        foreach($referralRates as $rate) {
            $rate->agent_id=$model->id;
            $rate->parent_agent_id=$model->parent_id;
            $rate->save();
        }

        $trx->commit();
    }

    private function getReferralRates($model) {
        $referralRates=array();
        foreach(Operator::getComboList() as $k=>$v) {
            $m=AgentReferralRate::model()->findByAttributes(array('agent_id'=>$model->id,'operator_id'=>$k));

            if (!$m) {
                $m=new AgentReferralRate();
                $m->operator_id=$k;
            }

            $referralRates[$k]=$m;
        }

        return $referralRates;
    }

    private function validate(&$model,&$user,&$referralRates) {
        $result=array();

        if (isset($_POST['Agent']))
            $model->setAttributes($_POST['Agent']);

        $model->validate();

        foreach($model->getErrors() as $attribute=>$errors)
            $result[CHtml::activeId($model,$attribute)]=$errors;

        if (isset($_POST['User']))
            $user->setAttributes($_POST['User']);

        $user->validate();

        foreach($user->getErrors() as $attribute=>$errors)
            $result[CHtml::activeId($user,$attribute)]=$errors;

        foreach($referralRates as $rate)
        {
            if (isset($_POST['AgentReferralRate'][$rate->operator_id]))
                $rate->setAttributes($_POST['AgentReferralRate'][$rate->operator_id]);

            if (!$model->id) $rate->agent_id=0;else $rate->agent_id=$model->id;

            $rate->validate();
            foreach($rate->getErrors() as $attribute=>$errors)
                $result[CHtml::activeId($rate,'['.$rate->operator_id.']'.$attribute)]=$errors;
        }

        return $result;
    }

    public function actionCreate()
    {
        $model = new Agent;
        $user = new User('create');
        $user->status = ModelLoggableBehavior::STATUS_ACTIVE;

        $referralRates=$this->getReferralRates($model);

        if (Yii::app()->getRequest()->getIsAjaxRequest()) {
            $result=$this->validate($model,$user,$referralRates);
            echo function_exists('json_encode') ? json_encode($result) : CJSON::encode($result);
            Yii::app()->end();
        }

        if (isset($_POST['Agent'])) {
            if (!Yii::app()->user->getState('isAdmin')) $model->parent_id=Yii::app()->user->getState('agentId');

            $errors=$this->validate($model,$user,$referralRates);

            if (empty($errors)) {
                $user->encryptPwd();
                $this->save($model,$user,$referralRates);

                $this->redirect(array('admin'));
            }
        }

        $this->render('create', array(
            'model' => $model,
            'user' => $user,
            'referralRates'=>$referralRates));
    }

    public function actionUpdate($id)
    {
        $model = $this->loadModel($id, 'Agent');
        $this->checkAgentPermissions($model);
        $user = $model->user;
        $password = $user->password;

        $referralRates=$this->getReferralRates($model);

        if (Yii::app()->getRequest()->getIsAjaxRequest()) {
            $result=$this->validate($model,$user,$referralRates);
            echo function_exists('json_encode') ? json_encode($result) : CJSON::encode($result);
            Yii::app()->end();
        }

        if (isset($_POST['Agent'])) {
            $errors=$this->validate($model,$user,$referralRates);

            if (empty($errors)) {
                if ($user->password != '') {
                    $user->encryptPwd();
                } else {
                    $user->password = $password;
                }

                $this->save($model,$user,$referralRates);

                $this->redirect(array('admin'));
            }
        }

        $user->password = '';
        $this->render('update', array(
            'model' => $model,
            'user' => $model->user,
            'referralRates' => $referralRates
        ));
    }

    public function addPaymentAllowed($model) {
        return (Yii::app()->user->getState('isAdmin') && $model->parent_id=='') ||
            (Yii::app()->user->getState('agentId')==$model->parent_id);
    }

    public function actionView($id)
    {
        $model = $this->loadModel($id, 'Agent');
        if ($model->id!=Yii::app()->user->getState('agentId'))
            $this->checkAgentPermissions($model);

        if ($this->addPaymentAllowed($model)) {
            $paymentNew= new Payment();
            $paymentNew->dt=new EDateTime();
            $paymentNew->agent_id=$model->id;
            $paymentNew->type=Payment::TYPE_NORMAL;
            $this->performAjaxValidation($paymentNew,'payment-form');

            if (isset($_POST['Payment'])) {
                $paymentNew->setAttributes($_POST['Payment']);
                if ($paymentNew->validate()) {
                    $paymentNew->save();
                    $model->recalcBalance();
                    $model->save();
                    $this->redirect(array('view','id'=>$model->id));
                }
            }
        }

        $sql="(select id,dt,sum,comment,0 as type from payment where agent_id=:agent_id) union
         (select id,dt,-sum as sum,'' as comment, 1 as type from delivery_report where agent_id=:agent_id)";
        $params=array(':agent_id'=>$id,':agent_id'=>$id);
        $count=Yii::app()->db->createCommand("select count(*) from ($sql) as mytab")->queryScalar($params);
        $logDataProvider=new CSqlDataProvider($sql,
        array(
            'params'=>$params,
            'totalItemCount'=>$count,
            'sort'=>array(
                'defaultOrder'=>'dt',
                'attributes'=>array('id','dt','comment','sum')
            ),
            'pagination'=>array('pageSize'=>BaseGxActiveRecord::ITEMS_PER_PAGE)
        ));

        $this->render('view', array(
            'model' => $model,
            'logDataProvider'=>$logDataProvider,
            'paymentNew' => $paymentNew
        ));
    }

    public function actionDelete($id)
    {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            try {
                $model = $this->loadModel($id, 'Agent');
                $this->checkAgentPermissions($model);
                $user = $model->user;
                $model->delete();
                $user->delete();
            } catch (CDbException $e) {
                $this->ajaxError(Yii::t("app", "Can't delete this object because it is used by another object(s)"));
            }

            if (!Yii::app()->getRequest()->getIsAjaxRequest())
                $this->redirect(array('admin'));
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    public function actionAdmin()
    {
        $model = new Agent('search');
        $model->unsetAttributes();

        if (isset($_GET['Agent']))
            $model->setAttributes($_GET['Agent']);

        $dataProvider=$model->search();

        if (Yii::app()->user->getState('isAdmin')) {
            $dataProvider->criteria->addCondition('parent_id is null');
        } else {
            $dataProvider->criteria->addColumnCondition(array('parent_id'=>
            Yii::app()->user->getState('agentId')));
        }

        $this->render('admin', array(
            'model' => $model,
            'dataProvider' => $dataProvider
        ));
    }

}