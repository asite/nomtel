<?php

class SupportOperatorController extends BaseGxController
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

    public function actionView() {
        if (isset($_POST['setnumbers'])) {
            $criteria = new CDbCriteria();
            $criteria->addCondition('support_operator_id is NULL');
            $criteria->addColumnCondition(array('status' => Number::STATUS_FREE));
            $criteria->order = Number::getBalanceStatusOrder();
            $criteria->limit = "20";
            if (Number::model()->count($criteria)>0) {
                Number::model()->updateAll(array('support_operator_id' => $_GET['id'],'support_operator_got_dt' => new CDbExpression('NOW()')), $criteria);
                Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Данные успешно переданы.');
            } else Yii::app()->user->setFlash('error', '<strong>Ошибка</strong> Нет данных для обработки.');
            $this->refresh();
        }

        $model = new Number('search');
        $model->unsetAttributes();
        if(isset($_GET['Number'])){
            $model->setAttributes($_GET['Number']);
        }

        $supportOperator = SupportOperator::model()->findByPk($_GET['id']);
        $data = $supportOperator->getNumbersStats();

        $ticketsStats=$supportOperator->getTicketsStats();

        $dataProvider = $model->search();
        $dataProvider->criteria->compare('support_operator_id',$_GET['id']);
        $dataProvider->criteria->order = 'support_operator_got_dt DESC';

        $this->render('view',array(
            'model'=>$model,
            'dataProvider'=>$dataProvider,
            'data'=>$data,
            'supportOperator'=>$supportOperator,
            'ticketsStats'=>$ticketsStats
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