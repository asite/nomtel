<?php

class TariffController extends BaseGxController
{


    public function actionCreate()
    {
        $model = new Tariff;
        $model->operator_id=$_REQUEST['parent_id'];

        $this->performAjaxValidation($model, 'tariff-form');

        if (isset($_POST['Tariff'])) {
            $model->setAttributes($_POST['Tariff']);

            if ($model->validate()) {
                $model->save();
                if (Yii::app()->getRequest()->getIsAjaxRequest())
                    Yii::app()->end();
                else
                    $this->redirect(array('operator/update','id'=>$model->operator_id));
            }
        }

        $this->render('create', array('model' => $model));
    }

    public function actionUpdate($id)
    {
        $model = $this->loadModel($id, 'Tariff');

        $this->performAjaxValidation($model, 'tariff-form');

        if (isset($_POST['Tariff'])) {
            $model->setAttributes($_POST['Tariff']);

            if ($model->validate()) {
                $model->save();
                $this->redirect(array('operator/update','id'=>$model->operator_id));
            }
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    public function actionDelete($id)
    {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            try {
                $this->loadModel($id, 'Tariff')->delete();
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
        $model = new Tariff('search');
        $model->unsetAttributes();

        if (isset($_GET['Tariff']))
            $model->setAttributes($_GET['Tariff']);

        $this->render('admin', array(
            'model' => $model,
        ));
    }

}