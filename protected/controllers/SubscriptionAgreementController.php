<?php

class SubscriptionAgreementController extends BaseGxController {

    public function actionStartCreate($sim_id)
    {
        $sim=$this->loadModel($sim_id,'Sim');
        $this->checkPermissions('createSubscriptionAgreement',array('sim'=>$sim));

        // reserve id
        $agreement=new SubscriptionAgreement();
        $agreement->save();
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
                $data[CHtml::activeId($person,$attr)]=strval($val);
            }

        echo function_exists('json_encode') ? json_encode($data) : CJSON::encode($data);
        Yii::app()->end();
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
            if (empty($errors)) {
                $trx=Yii::app()->db->beginTransaction();

                $person->save();

                $number->status=Number::STATUS_CONNECTED;
                $number->save();

                $agreement->setScenario('create');
                $agreement->person_id=$person->id;
                $agreement->number_id=$number->id;
                $agreement->save();

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