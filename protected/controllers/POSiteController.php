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
        $data=array();

        $number=Number::model()->findByPk(loggedNumberId());
        $data['number']=$number;

        $sim=Sim::model()->findByAttributes(array('parent_id'=>$number->sim_id,'agent_id'=>null));
        $data['sim']=$sim;

        $needPassport=true;
        $agreement=SubscriptionAgreement::model()->find(array(
            'condition'=>'number_id=:number_id',
            'params'=>array('number_id'=>$number->id),
            'order'=>'id desc'
        ));
        if ($agreement && $agreement->person && !empty($agreement->person->files)) $needPassport=false;

        $data['needPassport']=$needPassport;

        if ($needPassport) $this->processPassport($data);

        $this->render('index',$data);
    }

    private function processPassport(&$data) {
        $number=$data['number'];
        $agreement=SubscriptionAgreement::model()->find(array(
            'condition'=>'number_id=:number_id',
            'order'=>'id desc',
            'params'=>array(':number_id'=>$number->id)
        ));
        $person=$agreement->person;

        if (!$person) $person=new Person;
        $data['person']=$person;

        $person_files=array();

        foreach($person->files as $file)
            $person_files[]=$file->getUploaderInfo();

        $data['person_files']=json_encode($person_files);

        if (isset($_POST['person_files'])) {
            $person_files=array();
            foreach(explode(',',$_POST['person_files']) as $file_id)
                if ($file_id && File::getIdFromProtected($file_id)) $person_files[]=File::getIdFromProtected($file_id);

            $person_files_json=array();
            foreach($person_files as $file_id) {
                $file=File::model()->findByPk($file_id);
                $person_files_json[]=$file->getUploaderInfo();
            }
            $data['person_files']=json_encode($person_files_json);
        }

        if (!isset($_POST['Person'])) return;

        $person->setAttributes($_POST['Person']);
        if (!$person->validate()) return;

        $trx=Yii::app()->db->beginTransaction();

        $number->support_passport_need_validation=1;
        $number->support_status=count($person_files)>0 ? Number::SUPPORT_STATUS_ACTIVE:Number::SUPPORT_STATUS_PREACTIVE;

        if (!$number->support_operator_id) $number->support_operator_id=SupportOperator::OPERATOR_GUDKOV_ID;

        if (!$agreement) {
            $number->status=Number::STATUS_ACTIVE;
            $number->save();

            $person->save();

            foreach($person_files as $file_id) {
                $personFile=new PersonFile();
                $personFile->person_id=$person->id;
                $personFile->file_id=$file_id;
                $personFile->save();
            }

            $agreement=new SubscriptionAgreement();
            $agreement->save();
            $agreement->dt=new EDateTime();
            $agreement->fillDefinedId();
            $agreement->person_id=$person->id;
            $agreement->number_id=$number->id;
            $agreement->save();

            NumberHistory::addHistoryNumber($number->id,'Оформлен договор {SubscriptionAgreement:'.$agreement->id.'}');
        } else {
            $number->save();

            // save person files
            PersonFile::model()->deleteAll('person_id=:person_id',array(':person_id'=>$person->id));
            foreach($person_files as $file_id) {
                $personFile=PersonFile::model()->findByAttributes(array('person_id'=>$person->id,'file_id'=>$file_id));
                if (!$personFile) {
                    $personFile=new PersonFile();
                    $personFile->person_id=$person->id;
                    $personFile->file_id=$file_id;
                    $personFile->save();
                }
            }

            $person->save();
            NumberHistory::addHistoryNumber($number->id,'Отредактирован договор {SubscriptionAgreement:'.$agreement->id.'}');
        }

        $trx->commit();

        Yii::app()->user->setFlash('success','Ваши данные сохранены');
        $this->redirect('index');
    }
}