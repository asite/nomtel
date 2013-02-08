<?php

class SupportController extends BaseGxController
{

    public function additionalAccessRules() {
        return array(
            array('allow', 'actions' => array('number'), 'roles' => array('agent','support')),
            array('allow', 'actions' => array('numberStatus'), 'roles' => array('support')),
            array('allow', 'actions' => array('callback'), 'roles' => array('support')),
            array('allow', 'actions' => array('statistic'), 'roles' => array('support')),
        );
    }

    public function actionNumber() {
        $report = new NumberReport();
        $this->performAjaxValidation($report);

        if (isset($_POST['NumberReport'])) {
            $report->setAttributes($_POST['NumberReport']);

            if ($report->validate()) {
                $report_number=time();
                $report_dt=new EDateTime();

                $number=Number::model()->findByAttributes(array('number'=>$report->number));
                $agent=Agent::model()->findByPk(loggedAgentId());
                $body = $this->renderPartial('numberMail', array(
                    'report' => $report,
                    'number' => $number,
                    'agent' => $agent,
                    'report_number' => $report_number,
                    'report_dt' => $report_dt
                ), true);

                $recipients=Yii::app()->params['supportEmail'];
                if (!is_array($recipients)) $recipients=array($recipients);
                if ($agent->email!='') $recipients[]=$agent->email;

                $mail = new YiiMailMessage();
                $mail->setSubject(Yii::t('app', 'Problem with number'));
                $mail->setFrom(Yii::app()->params['supportEmailFrom']);
                $mail->setTo($recipients);
                $mail->setBody($body);

                $message = "Ваше обращение $report_number принято. Срок рассмотрения 24 часа. Спасибо";
                $message = urlencode($message);
                file_get_contents("http://api.infosmska.ru/interfaces/SendMessages.ashx?login=ghz&pwd=zerozz&phones=7$report->abonent_number&message=$message&sender=nomtel");

                if (Yii::app()->mail->send($mail))
                    Yii::app()->user->setFlash('success', Yii::t('app', "Problem report sent to support, report has number '%number'",array('%number%'=>$report_number)));
                else
                    Yii::app()->user->setFlash('error', Yii::t('app', 'Problem sending email'));

                $this->redirect(array('number'));
            }
        }

        $this->render('number', array(
            'report' => $report
        ));
    }

    public function actionNumberStatus() {
        $number=new NumberSupportNumber();
        $status=new NumberSupportStatus($_POST['NumberSupportStatus']['status']);
        $status->status=Number::SUPPORT_STATUS_ACTIVE;
        $person=new Person();

        $data=array('number'=>$number,'status'=>$status,'person'=>$person,'showStatusForm'=>false);

        if (isset($_POST['NumberSupportNumber']) || isset($_GET['number'])) {
            $_POST['NumberSupportNumber']['number']=preg_replace('%[^0-9]%','',$_POST['NumberSupportNumber']['number']);
            $_POST['NumberSupportNumber']['number']=preg_replace('%^8%','',$_POST['NumberSupportNumber']['number']);

            $number->setAttributes($_POST['NumberSupportNumber']);
            if ($_POST['NumberSupportNumber']['number']=='' && isset($_GET['number'])) $number->setAttributes(array('number'=>$_GET['number']));

            $numberObj=Number::model()->findByAttributes(array('number'=>$number->number));
            $data['numberObj']=$numberObj;

            if ($numberObj && $numberObj->status!=Number::STATUS_FREE) {
                $agreement=SubscriptionAgreement::model()->find(array(
                    'condition'=>'number_id=:number_id',
                    'order'=>'id desc',
                    'params'=>array(':number_id'=>$numberObj->id)
                ));
                $person=$agreement->person;
                $data['person']=$person;
                $person_files=array();
                foreach($person->files as $file)
                    $person_files[]=$file->getUploaderInfo();

                $data['person_files']=json_encode($person_files);
            }

            if (isset($_POST['person_files'])) {
                $person_files=array();
                foreach(explode(',',$_POST['person_files']) as $file_id)
                    if ($file_id) $person_files[]=$file_id;

                $person_files_json=array();
                foreach($person_files as $file_id) {
                    $file=File::model()->findByPk($file_id);
                    $person_files_json[]=$file->getUploaderInfo();
                }
                $data['person_files']=json_encode($person_files_json);
            }

            if ($numberObj) {
                $status->getting_passport_variant=$numberObj->support_getting_passport_variant;
                $status->number_region_usage=$numberObj->support_number_region_usage;
            }

            if (isset($_POST['NumberSupportStatus'])) {
                $status->setAttributes($_POST['NumberSupportStatus']);
                $person->setAttributes($_POST['Person']);
            }

            if ($number->validate()) {
                $data['showStatusForm']=true;

                // redirect to get method
                if (isset($_POST['findNumber'])) $this->redirect(array('numberStatus','number'=>$number->number));

                if (isset($_POST['NumberSupportStatus'])) {
                    $status->setAttributes($_POST['NumberSupportStatus']);
                    $person->setAttributes($_POST['Person']);

                    if ($status->validate()) {
                        $number=Number::model()->findByAttributes(array('number'=>$number->number));
                        $number->support_operator_id=loggedSupportOperatorId();

                        // do not change support status if number already has subscription agreement
                        if (!$numberObj) {
                            $number->support_status=$status->status;
                            $number->support_dt=new EDateTime();
                        }

                        if ($status->status==Number::SUPPORT_STATUS_CALLBACK) {
                            $number->support_callback_dt=new EDateTime($status->callback_dt);
                            $number->support_callback_name=$status->callback_name;
                        }

                        switch ($status->status) {
                            case Number::SUPPORT_STATUS_REJECT:
                            case Number::SUPPORT_STATUS_UNAVAILABLE:
                            case Number::SUPPORT_STATUS_CALLBACK:
                                $number->save();
                                Yii::app()->user->setFlash('success','Данные сохранены');
                                $this->redirect(array('numberStatus'));
                                break;
                            case Number::SUPPORT_STATUS_ACTIVE:
                                if (!$person->validate()) break;

                                $trx=Yii::app()->db->beginTransaction();


                                if (!$agreement) {
                                    $number->status=Number::STATUS_ACTIVE;
                                    $number->support_getting_passport_variant=$status->getting_passport_variant;
                                    $number->support_number_region_usage=$status->number_region_usage;


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
                                    $number->support_getting_passport_variant=$status->getting_passport_variant;
                                    $number->support_number_region_usage=$status->number_region_usage;
                                    $number->save();

                                    // save person files
                                    PersonFile::model()->deleteAll('person_id=:person_id',array(':person_id'=>$person->id));
                                    foreach($person_files as $file_id) {
                                        $personFile=new PersonFile();
                                        $personFile->person_id=$person->id;
                                        $personFile->file_id=$file_id;
                                        $personFile->save();
                                    }

                                    $person->save();
                                    NumberHistory::addHistoryNumber($number->id,'Отредактирован договор {SubscriptionAgreement:'.$agreement->id.'}');
                                }

                                $trx->commit();

                                Yii::app()->user->setFlash('success','Данные сохранены');
                                $this->redirect(array('numberStatus'));

                            case Number::SUPPORT_STATUS_SERVICE_INFO:
                                $number->save();
                                $this->redirect(array('number/edit','id'=>$number->id));
                                break;
                        }
                    }
                }
            }
        }

        $this->render('numberStatus', $data);
    }

    public function actionCallback() {
        $model = new Number('search');
        $model->unsetAttributes();
        if(isset($_GET['Number'])){
            $model->setAttributes($_GET['Number']);
        }

        $dataProvider = $model->search();
        $dataProvider->criteria->addColumnCondition(array('support_status' => 'CALLBACK'));
        $dataProvider->criteria->order = "support_callback_dt ASC";

        $this->render('callback',array(
            'model'=>$model,
            'dataProvider'=>$dataProvider
        ));
    }
    public function actionStatistic() {
        $model = Yii::app()->db->createCommand()
                                ->select('count(support_status) as count, support_status')
                                ->from('number')
                                ->where('support_operator_id=:val', array(':val'=>loggedSupportOperatorId()))
                                ->group('support_status')
                                ->queryAll();

        $data = Number::getSupportStatusArray();
        $supportOperator = SupportOperator::model()->findByPk(loggedSupportOperatorId());
        foreach ($model as $v) {
            $data[$v['support_status']]=$v['count'];
        }

        $this->render('statistic',array(
            'data'=>$data,
            'supportOperator'=>$supportOperator
        ));
    }
}
