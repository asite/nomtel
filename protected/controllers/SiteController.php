<?php

class SiteController extends Controller
{

    public function additionalAccessRules()
    {
        return array(
            array('allow', 'actions' => array('error', 'login', 'loginPO','restorePasswordPO','logout'), 'users' => array('*')),
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
        if (!Yii::app()->user->isGuest) $this->redirect(array('site/index'));

        if (isPO()) $this->redirect(array('loginPO'));

        $this->layout = '/layout/simple';

        if (isset($_REQUEST['loginForm'])) {
            $identity = new UserIdentity($_REQUEST['loginForm']['username'],
                $_REQUEST['loginForm']['password']);
            if ($identity->authenticate()) {
                Yii::app()->user->login($identity,
                    $_REQUEST['loginForm']['remember'] == 1 ? 3600 * 24 * 7 : 0);
                $this->redirect(array('index'));
            }
            Yii::app()->user->setFlash('loginError', $identity->errorMessage);

        }

        if (!Yii::app()->user->isGuest)
            $this->redirect(array('site/index'));

        $this->render('login');
    }

    public function actionLoginPO()
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
            $this->redirect(array('site/index'));

        $loginForm->password='';
        $this->render('loginPO',array('model'=>$loginForm));
    }

    public function actionRestorePasswordPO()
    {
        $this->layout = '/layout/simple';

        $model=new PORestorePasswordForm();
        if (isset($_POST['PORestorePasswordForm'])) {
            $model->setAttributes($_POST['PORestorePasswordForm']);

            if ($model->validate()) {
                $number=Number::model()->findByAttributes(array('number'=>(Number::getNumberFromFormatted($model->number))));
                $number->restorePassword();
                Yii::app()->user->setFlash('success','Новый пароль выслан вам по SMS');
                $this->redirect(array('restorePasswordPO'));
            }
        }

        $this->render('restorePasswordPO',array('model'=>$model));
    }

    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(array('site/login'));
    }

    public function actionIndex()
    {
        $this->render('index');
    }
}