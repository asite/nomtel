<?php

class OperatorController extends BaseGxController
{


    public function actionCreate()
    {
        $model = new Operator;

        $this->performAjaxValidation($model, 'operator-form');

        if (isset($_POST['Operator'])) {
            $model->setAttributes($_POST['Operator']);

            if ($model->validate()) {
                $model->save();
                if (Yii::app()->getRequest()->getIsAjaxRequest())
                    Yii::app()->end();
                else
                    $this->redirect(array('admin'));
            }
        }

        $this->render('create', array('model' => $model));
    }

    public function actionUpdate($id)
    {
        $model = $this->loadModel($id, 'Operator');
        $model2 = new Tariff('search');
        $model2->unsetAttributes();
        $model2->operator_id=$model->id;

        $this->performAjaxValidation($model, 'operator-form');

        if (isset($_POST['Operator'])) {
            $model->setAttributes($_POST['Operator']);

            if ($model->validate()) {
                $model->save();
                $this->redirect(array('admin'));
            }
        }

        $this->render('update', array(
            'model' => $model,
            'model2' => $model2
        ));
    }

    public function actionDelete($id)
    {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            try {
                $this->loadModel($id, 'Operator')->delete();
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
        $model = new Operator('search');
        $model->unsetAttributes();

        if (isset($_GET['Operator']))
            $model->setAttributes($_GET['Operator']);

        $this->render('admin', array(
            'model' => $model,
        ));
    }

}