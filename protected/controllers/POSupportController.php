<?php

class POSupportController extends Controller
{

    public function additionalAccessRules()
    {
        return array(
            array('allow', 'actions' => array('error', 'login', 'list', 'restorePassword','logout'), 'users' => array('*')),
            array('allow', 'actions' => array('index','sendRestoreCard','sendChangeTariff','sendBlock','sendOtherQuestion','sendSpecification'), 'users' => array('@')),
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
        $data['otherquestion'] = new OtherQuestion;
        $data['model']= new POSSpecification;
        $this->render('index',$data);
    }

    public function actionList() {
        $criteria = new CDbCriteria();
        $criteria->addCondition("number_id=:number_id");
        $criteria->params = array(':number_id'=>loggedNumberId());

        $dataProvider = new CActiveDataProvider('Ticket', array('criteria' => $criteria));

        $this->render('list', array(
            'dataProvider' => $dataProvider
        ));
    }


   /* private function sendEMail($message) {
        $number=Number::model()->findByPk(loggedNumberId());

        $report = array();
        $report['message'] = $message;
        $report['number'] = $report['problem_number'] = $number->number;
        $report['report_number'] = time();
        $report['dt']=new EDateTime();

        $body = $this->renderPartial('numberMail', array('data' => $report), true);

        $recipients=Yii::app()->params['supportEmail'];
        if (!is_array($recipients)) $recipients=array($recipients);

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
    */
    private function sendSMS($idTicket) {
        $number=Number::model()->findByPk(loggedNumberId());

        $message = "Ваше обращение #".$idTicket." принято. Срок рассмотрения 24 часа. Спасибо!";
        Sms::send($number->number,$message);

        Yii::app()->user->setFlash('success', Yii::t('app', "Problem report sent to support, report has number '%number'",array('%number%'=>$idTicket)));
    }

    public function actionSendRestoreCard() {
        $message = Yii::t('app','Restore the card');

        $idTicket = Ticket::addMessage(loggedNumberId(), $message);
        $this->sendSMS($idTicket);

        $this->redirect(array('index'));
    }

    public function actionSendChangeTariff() {
        $message = Yii::t('app','Change the tariff plan');

        $idTicket = Ticket::addMessage(loggedNumberId(), $message);
        $this->sendSMS($idTicket);

        $this->redirect(array('index'));
    }

    public function actionSendBlock() {
        $message = Yii::t('app','Block');

        $idTicket = Ticket::addMessage(loggedNumberId(), $message);
        $this->sendSMS($idTicket);

        $this->redirect(array('index'));
    }

    public function actionSendOtherQuestion() {
        if (isset($_POST['OtherQuestion'])) {
            $model = new OtherQuestion;
            $this->performAjaxValidation($model, 'send-other-question');

            $message = $_POST['OtherQuestion']['text'];
            $idTicket = Ticket::addMessage(loggedNumberId(), $message);
            $this->sendSMS($idTicket);
        }
        $this->redirect(array('index'));
    }

    public function actionSendSpecification() {
        if (isset($_POST['POSSpecification'])) {
            $model = new POSSpecification;
            $model->setAttributes($_POST['POSSpecification']);
            $this->performAjaxValidation($model,'specification');

            if ($model->validate()) {
                $message = Yii::t('app','Order details')." по датам ".$model->dateRange;

                $idTicket = Ticket::addMessage(loggedNumberId(), $message);
                $this->sendSMS($idTicket);
            }

            $this->redirect(array('index'));
        }
    }

}