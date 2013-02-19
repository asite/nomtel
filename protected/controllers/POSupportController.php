<?php

class POSupportController extends Controller
{

    public function additionalAccessRules()
    {
        return array(
            array('allow', 'actions' => array('error', 'login', 'restorePassword','logout','sendRestoreCard','sendChangeTariff','sendBlock','sendOtherQuestion','sendSpecification'), 'users' => array('*')),
            array('allow', 'actions' => array('index'), 'users' => array('@')),
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

    public function actionIndex()
    {
        $data['model']= new POSSpecification;
        $this->render('index',$data);
    }


    private function sendEMail($message) {
        $number=Number::model()->findByPk(loggedNumberId());

        $report = array();
        $report['message'] = $message;
        $report['number'] = $report['problem_number'] = $number->number;
        $report['report_number'] = time();
        $report['dt']=new EDateTime();

        $body = $this->renderPartial('numberMail', array('data' => $report), true);

        $recipients=Yii::app()->params['supportEmail'];
        if (!is_array($recipients)) $recipients=array($recipients);
        print_r($report['message']); exit;

        $mail = new YiiMailMessage();
        $mail->setSubject(Yii::t('app', 'Problem with number'));
        $mail->setFrom(Yii::app()->params['supportEmailFrom']);
        $mail->setTo($recipients);
        $mail->setBody($body);

        $message = "Ваше обращение ".$report['report_number']." принято. Срок рассмотрения 24 часа. Спасибо";
        Sms::send($report['number'],$report['report_number']);

        if (Yii::app()->mail->send($mail))
            Yii::app()->user->setFlash('success', Yii::t('app', "Problem report sent to support, report has number '%number'",array('%number%'=>$report['report_number'])));
        else Yii::app()->user->setFlash('error', Yii::t('app', 'Problem sending email'));
    }

    public function actionSendRestoreCard() {
        $this->sendEMail(Yii::t('app','Restore the card'));

        $this->redirect(array('index'));
    }

    public function actionSendChangeTariff() {
        $this->sendEMail(Yii::t('app','Change the tariff plan'));

        $this->redirect(array('index'));

    }

    public function actionSendBlock() {
        $this->sendEMail(Yii::t('app','Block'));

        $this->redirect(array('index'));
    }

    public function actionSendOtherQuestion() {
        $this->sendEMail(Yii::t('app','Other question'));

        $this->redirect(array('index'));
    }

    public function actionSendSpecification() {
        if (isset($_POST['POSSpecification'])) {
            $model = new POSSpecification;
            $model->setAttributes($_POST['POSSpecification']);
            $this->performAjaxValidation($model,'specification');

            $this->sendEMail(Yii::t('app','Order details')." по дате ".$model->dateRange);

            $this->redirect(array('index'));
        }
    }

}