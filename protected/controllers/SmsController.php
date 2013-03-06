<?php

class SmsController extends Controller
{

    public function additionalAccessRules()
    {
        return array(
            array('allow', 'actions' => array('error', 'send'), 'users' => array('@')),
        );
    }

    protected function performAjaxValidation($model, $id = '') {
        if (isset($_POST['ajax']) && $_POST['ajax'] === $id) {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionError() {
        $error = Yii::app()->errorHandler->error;
        $this->layout = '/layout/simple';

        $this->render('error', $error);
    }

    public function actionSend() {
        $model = new SmsLog;
        if (isset($_POST['SmsLog'])) {
            $this->performAjaxValidation($model,'send-sms');

            $model->number = Number::getNumberFromFormatted($_POST['SmsLog']['number']);
            $model->text = $_POST['SmsLog']['text'];
            $model->dt = new EDateTime();
            $model->user_id = Yii::app()->user->id;

            if ($model->save()) {
                Sms::send($model->number,$model->text);
                Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Смс-сообщение отправлено абоненту.');
            } else Yii::app()->user->setFlash('error', '<strong>Ошибка</strong> Произошла ошибка');
            $this->refresh();
        }

        $this->render('send',array(
            'model' => $model
        ));
    }
}