<?php

class SupportController extends BaseGxController
{

    public function additionalAccessRules() {
        return array(
            //array('allow', 'actions' => array('number'), 'roles' => array('agent')),
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
}
