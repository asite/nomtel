<?php

class SubscriptionAgreementController extends BaseGxController {

    public function actionStartCreate($sim_id)
    {
        $sim=$this->loadModel($sim_id,'Sim');
        $this->checkPermissions('createSubscriptionAgreement',array('sim'=>$sim));

        // reserve id
        $agreement=new SubscriptionAgreement();
        $agreement->save();
        $agreement->dt=new EDateTime();
        $agreement->fillDefinedId();
        $agreement->save();

        $this->redirect(array('create','id'=>$agreement->id,'sim_id'=>$sim_id));
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

    public function actionCreate($id,$sim_id)
    {
        $agreement=$this->loadModel($id,'SubscriptionAgreement');
        $sim=$this->loadModel($sim_id,'Sim');
        $number=Number::model()->findByAttributes(array('number'=>$sim->number));

        $this->checkPermissions('createSubscriptionAgreement',array('sim'=>$sim));

        $person=new Person();

        if (Yii::app()->getRequest()->getIsAjaxRequest()) {
            $result = $this->validate($agreement, $sim, $person);
            echo function_exists('json_encode') ? json_encode($result) : CJSON::encode($result);
            Yii::app()->end();
        }

        if (isset($_POST['Person'])) {
            $errors=$this->validate($agreement,$sim,$person);

            $person_files=array();
            foreach(explode(',',$_POST['person_files']) as $file_id)
                if ($file_id) $person_files[]=$file_id;

            $agreement_files=array();
            foreach(explode(',',$_POST['agreement_files']) as $file_id)
                if ($file_id) $agreement_files[]=$file_id;

            if (empty($errors)) {
                $trx=Yii::app()->db->beginTransaction();

                $person->save();

                $number->status=Number::STATUS_CONNECTED;
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
                $trx->commit();
                $this->redirect(array('sim/list'));
            }
        }

        $this->render('create',array(
            'sim'=>$sim,
            'agreement'=>$agreement,
            'person'=>$person
        ));
    }
}