<?php

class SiteController extends Controller
{

    public function additionalAccessRules()
    {
        return array(
            array('allow', 'actions' => array('error', 'login', 'loginPO','restorePasswordPO','logout'), 'users' => array('*')),
            array('allow', 'actions' => array('index','changeRole','forcedPasswordChange'), 'users' => array('@')),
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

    public function actionChangeRole($role) {
        Yii::app()->user->changeRole($role);
        $this->redirect(array('site/index'));
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

    public function actionForcedPasswordChange() {
        $this->layout = '/layout/simple';

        $model=new PasswordChange;

        if (isset($_POST['PasswordChange'])) {
            $model->setAttributes($_POST['PasswordChange']);
            if ($model->validate()) {
                if (Yii::app()->user->role=='agent') {
                    $trx=Yii::app()->db->beginTransaction();

                    $agent=Agent::model()->findByPk(loggedAgentId());
                    $agent->user->password=$model->password;
                    $agent->user->sendSmsWithLoginData($agent->phone_1);
                    $agent->user->encryptPwd();
                    $agent->require_password_change=0;
                    $agent->save();
                    $agent->user->save();

                    $trx->commit();

                    Yii::app()->user->setState('require_password_change',false);

                    $this->redirect(array('site/index'));
                }
            } else {
                $model->unsetAttributes();
            }
        }

        $this->render('forced_password_change',array('model'=>$model));
    }
}