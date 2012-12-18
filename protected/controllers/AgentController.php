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

        $sql="(select id,dt,sum,0 as type from payment where agent_id=:agent_id) union
         (select id,dt,-sum as sum,1 as type from delivery_report where agent_id=:agent_id)";
        $params=array(':agent_id'=>$id,':agent_id'=>$id);
        $count=Yii::app()->db->createCommand("select count(*) from ($sql) as mytab")->queryScalar($params);
        $logDataProvider=new CSqlDataProvider($sql,
        array(
            'params'=>$params,
            'totalItemCount'=>$count,
            'sort'=>array(
                'defaultOrder'=>'dt',
                'attributes'=>array('id','dt','sum')
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