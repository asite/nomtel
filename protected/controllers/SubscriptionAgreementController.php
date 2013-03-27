<?php

class SubscriptionAgreementController extends BaseGxController {

    public function additionalAccessRules()
    {
        return array(
            array('allow', 'roles' => array('agent')),
            array('allow', 'actions'=>array('update','create'), 'roles'=>array('support')),
        );
    }

    public function actionStartCreate($sim_id)
    {
        $sim=$this->loadModel($sim_id,'Sim');
        $number=$sim->numberObjectBySimId;
        $this->checkPermissions('createSubscriptionAgreement',array('parent_agent_id'=>$sim->parent_agent_id,'number_status'=>$number->status));

        // reserve id
        $agreement=new SubscriptionAgreement();
        $agreement->save();
        $agreement->dt=new EDateTime();
        $agreement->fillDefinedId();
        $agreement->save();

        $this->redirect(array('create','id'=>$agreement->id,'sim_id'=>$sim_id,'fullForm'=>0));
    }

    public function actionPassportSuggest() {
        $person=Person::model()->find(array(
            'condition'=>'passport_series=:passport_series and passport_number=:passport_number',
            'order'=>'id desc',
            'params'=>array(
                ':passport_series'=>$_POST['passport_series'],
                ':passport_number'=>$_POST['passport_number']
            )
        ));

        $data=array();

        if ($person)
            foreach($person->attributes as $attr=>$val) {
                $data['fields'][CHtml::activeId($person,$attr)]=strval($val);
            }

        foreach($person->files as $file)
            $data['person_files'][]=$file->getUploaderInfo();

        echo function_exists('json_encode') ? json_encode($data) : CJSON::encode($data);
        Yii::app()->end();
    }


    public function actionSaveFormInfo() {
        $sessionData=new SessionData(__CLASS__);
        $key=$sessionData->add($_POST);

        $data=array('url'=>$this->createUrl('downloadDocument',array("key"=>$key)));

        echo function_exists('json_encode') ? json_encode($data) : CJSON::encode($data);
        Yii::app()->end();
    }

    public function actionDownloadDocument($key) {
        $sessionData=new SessionData(__CLASS__);
        $data=$sessionData->get($key);
        $sessionData->delete($key);

        $agreement=$this->loadModel($data['id'],'SubscriptionAgreement');
        $sim=$this->loadModel($data['sim_id'],'Sim');
        $number=Number::model()->findByAttributes(array('number'=>$sim->number));
        $person=new Person;
        $person->setAttributes($data['Person']);

        DocumentGenerator::generate('subscription_agreement.docx','agreement_'.$agreement->defined_id.'.docx',array(
            'a'=>$agreement,
            's'=>$sim,
            'n'=>$number,
            'p'=>$person,
            'o'=>$sim->operator,
            'or'=>$sim->operatorRegion,
            't'=>$sim->tariff,
            'dt'=>$agreement->dt->format('d.m.Y')
        ));
    }

    public function actionGetBlank() {
        DocumentGenerator::generate('subscription_agreement.docx','subscription_agreement.docx',array(
        ));
    }

    private function validate($agreement,$sim,$person)
    {
        $errors=array();

        if (isset($_POST['Person'])) $person->setAttributes($_POST['Person']);

        $person->validate();

        foreach ($person->getErrors() as $attribute => $error)
            $errors[CHtml::activeId($person, $attribute)] = $error;

        return $errors;
    }

    public function actionUpdate($number_id)
    {
        $number=$this->loadModel($number_id,'Number');
        $agreement=SubscriptionAgreement::model()->find(array(
            'condition'=>'number_id=:number_id',
            'order'=>'id desc',
            'params'=>array(':number_id'=>$number->id)
        ));

        $sim=Sim::model()->find(array(
            'condition'=>'parent_id=:sim_id',
            'order'=>'id desc',
            'params'=>array(':sim_id'=>$number->sim_id)
        ));
        $person=$agreement->person;

        if (!$agreement) {
            $person=new Person();
            $person->save();

            $agreement=new SubscriptionAgreement();
            $agreement->save();
            $agreement->dt=new EDateTime();
            $agreement->fillDefinedId();
            $agreement->person_id=$person->id;
            $agreement->number_id=$number_id;
            $agreement->save();

            $this->refresh();
        }

        $this->checkPermissions('updateSubscriptionAgreement',array(
            'number_status'=>$number->status,
            'parent_agent_id'=>$sim->parent_agent_id,
        ));

        if (Yii::app()->getRequest()->getIsAjaxRequest()) {
            $result = $this->validate($agreement, $sim, $person);
            echo function_exists('json_encode') ? json_encode($result) : CJSON::encode($result);
            Yii::app()->end();
        }

        /*
        if (isset($_POST['Person'])) {
            $errors=$this->validate($agreement,$sim,$person);

            $person_files=array();
            foreach(explode(',',$_POST['person_files']) as $file_id)
                if ($file_id && File::getIdFromProtected($file_id)) $person_files[]=File::getIdFromProtected($file_id);

            $agreement_files=array();
            foreach(explode(',',$_POST['agreement_files']) as $file_id)
                if ($file_id && File::getIdFromProtected($file_id)) $agreement_files[]=File::getIdFromProtected($file_id);

            if (empty($errors)) {
                $trx=Yii::app()->db->beginTransaction();

                $person->save();
                $number->save();

                $agreement->setScenario('create');
                $agreement->save();

                // save person files
                PersonFile::model()->deleteAll('person_id=:person_id',array(':person_id'=>$person->id));
                foreach($person_files as $file_id) {
                    $personFile=new PersonFile();
                    $personFile->person_id=$person->id;
                    $personFile->file_id=$file_id;
                    $personFile->save();
                }

                // save agreement files
                SubscriptionAgreementFile::model()->deleteAll('subscription_agreement_id=:subscription_agreement_id',array(':subscription_agreement_id'=>$agreement->id));
                foreach($agreement_files as $file_id) {
                    $agreementFile=new SubscriptionAgreementFile();
                    $agreementFile->subscription_agreement_id=$agreement->id;
                    $agreementFile->file_id=$file_id;
                    $agreementFile->save();
                }

                NumberHistory::addHistoryNumber($number->id,'Отредактирован договор {SubscriptionAgreement:'.$agreement->id.'}');
                $trx->commit();
                if (Yii::app()->user->role=='support') {
                    $this->redirect(array('support/numberStatus'));
                } else {
                    $this->redirect(array('sim/list'));
                }
            }
        }
        */

        $person_files=array();
        if (isset($person->files))
            foreach($person->files as $file) $person_files[]=$file->getUploaderInfo();

        $agreement_files=array();
        if (isset($agreement->files))
            foreach($agreement->files as $file) $agreement_files[]=$file->getUploaderInfo();

        $this->render('update',array(
            'sim'=>$sim,
            'agreement'=>$agreement,
            'person'=>$person,
            'person_files'=>json_encode($person_files),
            'agreement_files'=>json_encode($agreement_files),
        ));
    }

    public function actionCreate($id,$sim_id,$fullForm)
    {
        $fullForm=$fullForm==1;

        $agreement=$this->loadModel($id,'SubscriptionAgreement');
        $sim=$this->loadModel($sim_id,'Sim');
        $number=Number::model()->findByAttributes(array('number'=>$sim->number));

        $this->checkPermissions('createSubscriptionAgreement',array('parent_agent_id'=>$sim->parent_agent_id,'number_status'=>$number->status));

        $person=new Person();
        if ($fullForm) $person->setScenario('activating');

        if (Yii::app()->getRequest()->getIsAjaxRequest()) {
            $result = $this->validate($agreement, $sim, $person);
            echo function_exists('json_encode') ? json_encode($result) : CJSON::encode($result);
            Yii::app()->end();
        }

        if (isset($_POST['person_files'])) {
            $errors=$this->validate($agreement,$sim,$person);

            $person_files=array();
            foreach(explode(',',$_POST['person_files']) as $file_id)
                if ($file_id && File::getIdFromProtected($file_id)) $person_files[]=File::getIdFromProtected($file_id);

            $agreement_files=array();
            foreach(explode(',',$_POST['agreement_files']) as $file_id)
                if ($file_id && File::getIdFromProtected($file_id)) $agreement_files[]=File::getIdFromProtected($file_id);

            if (empty($errors)) {
                $trx=Yii::app()->db->beginTransaction();

                $person->save();

                $number->status=Number::STATUS_ACTIVE;
                $number->save();

                $agreement->setScenario('create');
                $agreement->person_id=$person->id;
                $agreement->number_id=$number->id;
                $agreement->save();

                // save person files
                foreach($person_files as $file_id) {
                    $personFile=new PersonFile();
                    $personFile->person_id=$person->id;
                    $personFile->file_id=$file_id;
                    $personFile->save();
                }

                // save agreement files
                foreach($agreement_files as $file_id) {
                    $agreementFile=new SubscriptionAgreementFile();
                    $agreementFile->subscription_agreement_id=$agreement->id;
                    $agreementFile->file_id=$file_id;
                    $agreementFile->save();
                }

                NumberHistory::addHistoryNumber($number->id,'Оформлен договор {SubscriptionAgreement:'.$agreement->id.'}');

                if ($fullForm) {
                    $message = "Компания Номтел благодарит Вас за регистрацию.";
                    Sms::send($number->number,$message);
                } else {
                    $number->support_passport_need_validation=1;
                    $ticketId=Ticket::addMessage($number->id,'Оформить номер '.$number->number);
                    $ticket=Ticket::model()->findByPk($ticketId);
                    $ticket->internal=$ticket->text;
                    $ticket->status=Ticket::STATUS_IN_WORK_OPERATOR;
                    $ticket->type=Ticket::TYPE_OCR_DOCS;
                    $ticket->save();
                }

                $trx->commit();
                $this->redirect(array('sim/list'));
            }
        }

        $this->render('create',array(
            'sim'=>$sim,
            'agreement'=>$agreement,
            'person'=>$person,
            'fullForm'=>$fullForm
        ));
    }
}