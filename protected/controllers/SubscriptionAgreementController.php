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

    private function generateDocument($template,$filename,$data) {
        // disable web logging
        foreach (Yii::app()->log->routes as $route) {
            if ($route instanceof CWebLogRoute || $route instanceof CProfileLogRoute) {
                $route->enabled = false;
            }
        }
        Yii::app()->db->enableProfiling = false;

        $PHPWord = new PHPWord();

        $tempFileName=tempnam(Yii::getPathOfAlias('webroot.var.temp'),'phpword_');

        // open file in temp directory, phpword is creating temporary file in document folder
        copy(Yii::getPathOfAlias('application.data').'/'.$template,$tempFileName);
        $document = $PHPWord->loadTemplate($tempFileName);

        // assign variable values
        foreach($data as $key=>$val)
            $document->setValue($key,$val);

        // delete unitialized variables
        foreach($document->getVariables() as $key)
            $document->setValue($key,'');

        $document->save($tempFileName);

        $file=file_get_contents($tempFileName);

        unlink($tempFileName);

        header('Content-type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Pragma: private');
        header('Content-Disposition: attachment; filename="'.$filename.'"');

        echo $file;
        Yii::app()->end();
    }

    public function actionSaveFormInfo() {
        $_SESSION['doc'][$_REQUEST['id']]=$_POST;

        $data=array('url'=>$this->createUrl('downloadDocument',array("id"=>$_REQUEST['id'])));

        echo function_exists('json_encode') ? json_encode($data) : CJSON::encode($data);
        Yii::app()->end();
    }

    public function actionDownloadDocument($id) {
        $data=$_SESSION['doc'][$id];
        //unset($_SESSION['doc'][$id]);

        $agreement=$this->loadModel($data['id'],'SubscriptionAgreement');

        $this->generateDocument('subscription_agreement.docx','agreement_'.$agreement->defined_id.'.docx',array(
            'defined_id'=>$agreement->defined_id
        ));
    }

    public function actionGetBlank() {
        $this->generateDocument('subscription_agreement.docx','subscription_agreement.docx',array(
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

            if ($_POST['doctype']=='blank') {
                $this->generateDocument('subscription_agreement.docx','subscription_agreement.docx',array(
                ));
            }

            if ($_POST['doctype']=='doc') {
                $this->generateDocument('subscription_agreement.docx','subscription_agreement.docx',array(
                    'defined_id'=>$agreement->defined_id
                ));
            }


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