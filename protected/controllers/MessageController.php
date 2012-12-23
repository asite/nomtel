<?php

class MessageController extends Controller {

  protected function performAjaxValidation($model, $id){
    if(isset($_POST['ajax']) && $_POST['ajax']===$id) {
      echo CActiveForm::validate($model);
      Yii::app()->end();
    }
  }

  public function actionInbox() {
    $criteria = new CDbCriteria();
    $criteria->condition = "(whom=:whom)";
    $criteria->params = array(
      ':whom' => loggedAgentId()
    );
    $criteria->order = "status IN ('NEW','VIEWED','CLOSED'), status ASC, dt DESC";
    $dataProvider = new CActiveDataProvider('Ticket', array('criteria' => $criteria));

    $this->render('inbox',array('dataProvider'=>$dataProvider));
  }

  public function actionOutbox() {
    $criteria = new CDbCriteria();
    $criteria->condition = "(agent_id=:agent_id)";
    $criteria->params = array(
      ':agent_id' => loggedAgentId()
    );
    $criteria->order = 'status DESC, dt DESC';
    $dataProvider = new CActiveDataProvider('Ticket', array('criteria' => $criteria));

    $this->render('outbox',array('dataProvider'=>$dataProvider));
  }

  public function actionCreate() {
    $model = new Ticket;
    $model->ticketMessages = new TicketMessage;

    $agent = Agent::model()->getFullComboList();
    $agent = array(0=>Yii::t('app','Select Agent'))+$agent;

    if (isset($_POST['Ticket'])) {

      if ($_POST['Ticket']['whom']==0) $_POST['Ticket']['whom']='';
      $this->performAjaxValidation(array($model,$model->ticketMessages), 'create-message');

      $model->attributes = $_POST['Ticket'];
      $model->dt = time();
      $model->agent_id = loggedAgentId();

      $model->ticketMessages->agent_id = $model->agent_id;
      $model->ticketMessages->attributes = $_POST['TicketMessage'];
      $model->ticketMessages->dt = $model->dt;

      $model->title = $model->ticketMessages->message;
      $transaction=Yii::app()->db->beginTransaction();
      try{
        $model->save(false);
        $model->ticketMessages->ticket_id = $model->id;
        $model->ticketMessages->save();
        $transaction->commit();
        Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Сообщение отправлено.');
      }catch( Exception $e ){
        $transaction->rollback();
        throw $e;
      }
      $this->redirect($this->createUrl('outbox'));
      exit;
    }

    $this->render('create', array('model'=>$model,'agent'=>$agent));
  }

  public function actionView($id='') {
    $model = new TicketMessage;
    $messages = TicketMessage::model()->findAllByAttributes(array('ticket_id'=>$id),array('order'=>'dt DESC'));
    $agents = Agent::model()->getFullComboList(false);

    $this->render('view',array('model'=>$model,'messages'=>$messages,'agents'=>$agents));
  }

  public function actionAnswer($ticket) {
    if (isset($_POST['TicketMessage'])) {
      $model = new TicketMessage;
      $model->setAttributes($_POST['TicketMessage']);
      $this->performAjaxValidation($model, 'add-message');

      $model->agent_id = loggedAgentId();
      $model->ticket_id = $ticket;
      $model->dt = time();

      $transaction=Yii::app()->db->beginTransaction();
      try{
        $model->save(false);
        $modelTicket = Ticket::model()->findByPk($ticket);
        if ($modelTicket->agent_id != loggedAgentId()) {
          $modelTicket->status = 'VIEWED';
          $modelTicket->save();
        }
        $transaction->commit();
      }catch( Exception $e ){
        $transaction->rollback();
        throw $e;
      }
    }

    $this->redirect($this->createUrl('view',array('id'=>$ticket)));
  }

  public function actionClose($id='') {
      try {
        $model = Ticket::model()->findByPk($id);
        $model->status = 'CLOSED';
        $model->update();
        return true;
      } catch (CDbException $e) {
        $this->ajaxError(Yii::t("app", "Can't delete this object because it is used by another object(s)"));
      }
  }
}