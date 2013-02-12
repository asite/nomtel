<?php

class POSiteController extends Controller
{

    public function additionalAccessRules()
    {
        return array(
            array('allow', 'actions' => array('error', 'login', 'restorePassword','logout'), 'users' => array('*')),
            array('allow', 'actions' => array('index'), 'users' => array('@')),
        );
    }

    public function actionError()
    {
        $error = Yii::app()->errorHandler->error;
        $this->layout = '/layout/simple';

        $this->render('error', $error);
    }

    public function actionLogin()
    {
        $this->layout = '/layout/simple';

        if (!Yii::app()->user->isGuest) $this->redirect(array('site/index'));

        $loginForm=new POLoginForm();
        if (isset($_POST['POLoginForm'])) {
            $loginForm->setAttributes($_POST['POLoginForm']);

            if ($loginForm->validate()) {
                $identity = new UserIdentity(Number::getNumberFromFormatted($loginForm->number),$loginForm->password);
                if ($identity->authenticate()) {
                    Yii::app()->user->login($identity);
                    $this->redirect(array('index'));
                }
                $loginForm->addError('password',$identity->errorMessage);
            }
        }

        if (!Yii::app()->user->isGuest)
            $this->redirect(array('index'));

        $loginForm->password='';
        $this->render('login',array('model'=>$loginForm));
    }

    public function actionRestorePassword()
    {
        $this->layout = '/layout/simple';

        $model=new PORestorePasswordForm();
        if (isset($_POST['PORestorePasswordForm'])) {
            $model->setAttributes($_POST['PORestorePasswordForm']);

            if ($model->validate()) {
                $number=Number::model()->findByAttributes(array('number'=>(Number::getNumberFromFormatted($model->number))));
                $number->restorePassword();
                Yii::app()->user->setFlash('success','Новый пароль выслан вам по SMS');
                $this->redirect(array('restorePassword'));
            }
        }

        $this->render('restorePassword',array('model'=>$model));
    }

    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(array('login'));
    }

    public function actionIndex()
    {
        $this->render('index');
    }
}