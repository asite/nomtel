<?php

class SiteController extends Controller
{

    public function additionalAccessRules()
    {
        return array(
            array('allow', 'actions' => array('error', 'login', 'logout'), 'users' => array('*')),
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