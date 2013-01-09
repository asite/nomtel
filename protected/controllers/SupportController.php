<?php

class SupportController extends BaseGxController
{
    public function additionalAccessRules()
    {
        return array(
            array('allow', 'roles' => array('agent')),
        );
    }

    protected function performAjaxValidation($model, $id)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === $id) {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    private function save($model, $user)  {
        $trx = Yii::app()->db->beginTransaction();

        $user->save();

        $model->user_id = $user->id;
        $model->save();

        $trx->commit();
    }

    public function actionAdmin() {
        $model = new SupportOperator('search');
        $model->unsetAttributes();

        if (isset($_GET['SupportOperator']))
            $model->setAttributes($_GET['SupportOperator']);

        $dataProvider = $model->search();

        $this->render('admin', array(
            'model' => $model,
            'dataProvider' => $dataProvider
        ));
    }

    public function actionCreate() {
        $model = new SupportOperator;
        $user = new User('create');
        $user->status = ModelLoggableBehavior::STATUS_ACTIVE;

        if (isset($_POST['SupportOperator'])) {
            $this->performAjaxValidation(array($model,$user),'support-operator-form');
            $model->setAttributes($_POST['SupportOperator']);
            $user->setAttributes($_POST['User']);

            $user->encryptPwd();
            $this->save($model, $user);

            $this->redirect(array('admin'));
        }

        $this->render('create', array(
            'model' => $model,
            'user' => $user
        ));
    }

    public function actionDelete($id) {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            try {
                $trx = Yii::app()->db->beginTransaction();
                $model = $this->loadModel($id, 'SupportOperator');
                $user = $model->user;

                $model->delete();
                $user->delete();

                $trx->commit();
            } catch (CDbException $e) {
                $this->ajaxError(Yii::t("app", "Can't delete this Technical Support User because it is used in system"));
            }

            if (!Yii::app()->getRequest()->getIsAjaxRequest())
                $this->redirect(array('admin'));
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    public function actionUpdate($id) {
        $model = $this->loadModel($id, 'SupportOperator');
        $user = $model->user;
        $password = $user->password;

        if (isset($_POST['SupportOperator'])) {
            $this->performAjaxValidation(array($model,$user),'support-operator-form');
            $model->setAttributes($_POST['SupportOperator']);
            $user->setAttributes($_POST['User']);

            if ($user->password != '') {
                $user->encryptPwd();
            } else {
                $user->password = $password;
            }

            $this->save($model, $user);

            if ($id == adminAgentId()) {
                $this->redirect(array('update', 'id' => $id));
            } else {
                $this->redirect(array('admin'));
            }
        }

        $user->password = '';
        $this->render('update', array(
            'model' => $model,
            'user' => $model->user
        ));
    }
}