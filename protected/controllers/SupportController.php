<?php

class SupportController extends BaseGxController
{

    public function additionalAccessRules() {
        return array(
            array('allow', 'actions' => array('number'), 'roles' => array('agent')),
            array('allow', 'actions' => array('numberStatus'), 'roles' => array('support')),
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
        $person=new Person();

        $data=array('number'=>$number,'status'=>$status,'person'=>$person,'showStatusForm'=>false);

        if (isset($_POST['NumberSupportNumber']) || isset($_GET['number'])) {
            $_POST['NumberSupportNumber']['number']=preg_replace('%[^0-9]%','',$_POST['NumberSupportNumber']['number']);
            $_POST['NumberSupportNumber']['number']=preg_replace('%^8%','',$_POST['NumberSupportNumber']['number']);

            $number->setAttributes($_POST['NumberSupportNumber']);

            if (isset($_POST['NumberSupportStatus'])) {
                $status->setAttributes($_POST['NumberSupportStatus']);
                $person->setAttributes($_POST['Person']);
            }

            if (isset($_GET['number'])) $number->setAttributes(array('number'=>$_GET['number']));

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
                        $number->support_status=$status->status;
                        $number->support_dt=new EDateTime();

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

                                $number->status=Number::STATUS_ACTIVE;
                                $number->support_getting_passport_variant=$status->getting_passport_variant;
                                $number->support_number_region_usage=$status->number_region_usage;


                                $number->save();

                                $person->save();

                                $agreement=new SubscriptionAgreement();
                                $agreement->save();
                                $agreement->dt=new EDateTime();
                                $agreement->fillDefinedId();
                                $agreement->person_id=$person->id;
                                $agreement->number_id=$number->id;
                                $agreement->save();

                                NumberHistory::addHistoryNumber($number->id,'Оформлен договор {SubscriptionAgreement:'.$agreement->id.'}');

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
}
