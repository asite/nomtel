<?php

class AgentController extends BaseGxController
{

    public function actionCreate()
    {
        $model = new Agent;
        $user = new User('create');
        $user->status = ModelLoggableBehavior::STATUS_ACTIVE;

        $this->performAjaxValidation(array($model, $user));

        if (isset($_POST['Agent'])) {
            $model->setAttributes($_POST['Agent']);
            $user->setAttributes($_POST['User']);

            $validated = true;
            if (!$model->validate()) $validated = false;
            if (!$user->validate()) $validated = false;

            if ($validated) {
                $user->encryptPwd();
                $user->save();
                $model->user_id = $user->id;
                $model->save();
                if (Yii::app()->getRequest()->getIsAjaxRequest())
                    Yii::app()->end();
                else
                    $this->redirect(array('admin'));
            }
        }

        $this->render('create', array('model' => $model, 'user' => $user));
    }

    public function actionUpdate($id)
    {
        $model = $this->loadModel($id, 'Agent');
        $user = $model->user;
        $password = $user->password;

        $this->performAjaxValidation(array($model, $user));

        if (isset($_POST['Agent'])) {
            $model->setAttributes($_POST['Agent']);
            $user->setAttributes($_POST['User']);

            $validated = true;
            if (!$model->validate()) $validated = false;
            if (!$user->validate()) $validated = false;

            if ($validated) {
                $model->save();

                if ($user->password != '') {
                    $user->encryptPwd();
                } else {
                    $user->password = $password;
                }

                $user->save();
                $this->redirect(array('admin'));
            }
        }

        $user->password = '';
        $this->render('update', array(
            'model' => $model,
            'user' => $model->user
        ));
    }

    public function actionView($id)
    {
        if (!Yii::app()->user->getState('isAdmin') && $id!=Yii::app()->user->getState('agentId'))
            throw new CHttpException(400, Yii::t('giix', 'Your request is invalid.'));

        $model = $this->loadModel($id, 'Agent');

        if (Yii::app()->user->getState('isAdmin')) {
            $paymentNew= new Payment();
            $paymentNew->dt=new EDateTime();
            $paymentNew->agent_id=$model->id;
            $this->performAjaxValidation($paymentNew);

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

        $payment = new Payment('search');
        if (isset($_GET['Payment']))
            $payment->setAttributes($_GET['Payment']);
        $payment->agent_id = $id;

        $deliveryReport = new DeliveryReport('search');
        if (isset($_GET['DeliveryReport']))
            $deliveryReport->setAttributes($_GET['DeliveryReport']);
        $deliveryReport->agent_id = $id;


        $this->render('view', array(
            'model' => $model,
            'payment' => $payment,
            'deliveryReport' => $deliveryReport,
            'paymentNew' => $paymentNew
        ));
    }

    public function actionDelete($id)
    {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            try {
                $model = $this->loadModel($id, 'Agent');
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

        $this->render('admin', array(
            'model' => $model,
        ));
    }

}