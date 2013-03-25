<?php

class POSiteController extends Controller
{

    public function additionalAccessRules()
    {
        return array(
            array('allow', 'actions' => array('error', 'login', 'restorePassword','logout'), 'users' => array('*')),
            array('allow', 'actions' => array('index','tariff', 'static', 'orderSim','internet'), 'users' => array('@')),
        );
    }

    protected function performAjaxValidation($model, $id = '') {
        if (isset($_POST['ajax']) && $_POST['ajax'] === $id) {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
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


        $restore=isset($_POST['restore']);
        $loginForm=new POLoginForm($restore ? 'restore':'');
        if (isset($_POST['POLoginForm'])) {
            $loginForm->setAttributes($_POST['POLoginForm']);

            if ($loginForm->validate()) {
                if ($restore) {
                    $number=Number::model()->findByAttributes(array('number'=>(Number::getNumberFromFormatted($loginForm->number))));
                    if ($number->user && $number->user->last_password_restore &&
                        ($number->user->last_password_restore->modify("+5 minute")>new EDateTime())) {
                        Yii::app()->user->setFlash('error','Пароль можно восстанавливать не чаще одного раза в 5 минут');
                        $this->redirect(array('login'));
                    } else {
                        if (!$number->user_id) {
                            $user=User::model()->findByAttributes(array('username'=>$number->number));
                            if ($user) $number->user_id=$user->id;
                            $number->save();
                            $number->refresh();
                        }
                        $number->restorePassword();
                        Yii::app()->user->setFlash('success','Новый пароль выслан вам по SMS');
                        $this->redirect(array('login'));
                    }
                } else {
                    $identity = new UserIdentity(Number::getNumberFromFormatted($loginForm->number),$loginForm->password);
                    if ($identity->authenticate()) {
                        Yii::app()->user->login($identity);
                        $this->redirect(array('index'));
                    }
                    $loginForm->addError('password',$identity->errorMessage);
                }
            }
        }

        if (!Yii::app()->user->isGuest)
            $this->redirect(array('index'));

        $loginForm->password='';
        $this->render('login',array('model'=>$loginForm));
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
        if ($agreement && $number->support_passport_need_validation==0) $needPassport=false;

        $data['agreement']=$agreement;
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

        if (!isset($_POST['person_files'])) return;

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

        //if (!isset($_POST['Person'])) return;

        $person->setAttributes($_POST['Person']);
        if (!$person->validate()) return;

        $trx=Yii::app()->db->beginTransaction();

        $number->support_passport_need_validation=1;
        $number->support_status=count($person_files)>0 ? Number::SUPPORT_STATUS_ACTIVE:Number::SUPPORT_STATUS_PREACTIVE;

        if (!$number->support_operator_id) $number->support_operator_id=SupportOperator::OPERATOR_DEFAULT_ID;

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

            NumberHistory::addHistoryNumber($number->id,'Загружены изображения документа');
            //NumberHistory::addHistoryNumber($number->id,'Оформлен договор {SubscriptionAgreement:'.$agreement->id.'}');
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
            NumberHistory::addHistoryNumber($number->id,'Обновлены изображения документа');
            //NumberHistory::addHistoryNumber($number->id,'Отредактирован договор {SubscriptionAgreement:'.$agreement->id.'}');
        }

        $ticketId=Ticket::addMessage($number->id,'Оформить номер '.$number->number);
        $ticket=Ticket::model()->findByPk($ticketId);
        $ticket->internal=$ticket->text;
        $ticket->status=Ticket::STATUS_IN_WORK_OPERATOR;
        $ticket->type=Ticket::TYPE_OCR_DOCS;
        $ticket->save();

        $trx->commit();

        Yii::app()->user->setFlash('success','Ваши данные сохранены');
        $this->redirect('index');
    }

    public function actionTariff() {
        $data=array();
        $number=Number::model()->findByPk(loggedNumberId());
        $data=Sim::model()->findByAttributes(array('parent_id'=>$number->sim_id,'agent_id'=>null));

        $this->render('tariff',array('number'=>$number, 'sim'=>$data));
    }


    public function actionStatic($page) {

        $data=array();
        $number=Number::model()->findByPk(loggedNumberId());
        $data=Sim::model()->findByAttributes(array('parent_id'=>$number->sim_id,'agent_id'=>null));

        $this->render('static',array('number'=>$number, 'sim'=>$data, 'page'=>$page));
    }

    public function actionInternet($type) {
        $number=Number::model()->findByPk(loggedNumberId());

        if ($type=='connect') $message = "Абонент ".$number->number." желает подключить услугу Интернет";
        else $message = $message = "Абонент ".$number->number." желает отключить услугу Интернет";
        $idTicket = Ticket::addMessage(loggedNumberId(), $message);
        $num = time();
        Yii::app()->user->setFlash('success','Ваше обращение'.$num.' принято');
        Sms::send($number->number,'Ваше обращение'.$num.' принято');

        $this->redirect($this->createUrl('static',array('page'=>'internet')));
    }

    public function actionOrderSim() {
        $number = Number::model()->findByPk(loggedNumberId());
        $sim = Sim::model()->findByAttributes(array('number'=>$number->number, 'agent_id'=>NULL, 'is_active'=>1));

        $tk = time();

        if ($sim->parentAgent->taking_orders) {
            $sendNumber = $sim->parentAgent->phone_1;
            $sendMessage = "#$number->number хочет подключить ещё";
            $sendEmail = array($sim->parentAgent->email?$sim->parentAgent->email:Yii::app()->params['numberHelpEmail']);
        } else {
            $sendNumber = Yii::app()->params['adminPhone'];
            $sendMessage = "#$number->number хочет подключить ещё";
            $sendEmail = array(Yii::app()->params['numberHelpEmail']);
        }

        $mail = new YiiMailMessage();
        $mail->setSubject('Заказ симкарт');
        $mail->setFrom(Yii::app()->params['supportEmailFrom']);
        $mail->setTo($sendEmail);
        $mail->setBody($sendMessage);

        Sms::send($sendNumber,$sendMessage);

        if (Yii::app()->mail->send($mail))
            Yii::app()->user->setFlash('success', 'Номер Вашего обращения #'.$tk.'. Наш менеджер свяжется в ближайшее время.');
        else Yii::app()->user->setFlash('error', 'Произошла ошибка. Пожалуйста сообщите о ней администратору!');

        $this->redirect('index');
    }

}