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
    $agents = Agent::model()->getFullComboList();

    $this->render('inbox',array('dataProvider'=>$dataProvider,'agents'=>$agents));
  }

  public function actionOutbox() {
    $criteria = new CDbCriteria();
    $criteria->condition = "(agent_id=:agent_id)";
    $criteria->params = array(
      ':agent_id' => loggedAgentId()
    );
    $criteria->order = "status IN ('NEW','VIEWED','CLOSED'), status ASC, dt DESC";
    $dataProvider = new CActiveDataProvider('Ticket', array('criteria' => $criteria));

    $this->render('outbox',array('dataProvider'=>$dataProvider));
  }

  public function actionCreate() {
    $model = new Ticket;
    $model->ticketMessages = new TicketMessage;

    $agent = Agent::model()->getFullComboList();
    $agent = array(0=>Yii::t('app','Select Agent')) + $agent;

    if (isset($_POST['Ticket'])) {

      if ($_POST['Ticket']['whom']==0) $_POST['Ticket']['whom']='';
      $this->performAjaxValidation(array($model,$model->ticketMessages), 'create-message');

      $model->attributes = $_POST['Ticket'];
      $model->dt = new EDateTime();
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

  public function actionView($id='', $type='') {
    $ticket = Ticket::model()->findByPk($id);

    if ($ticket->agent_id!=loggedAgentId() && $ticket->whom!=loggedAgentId()) $this->redirect($this->createUrl('site/index'));

    $model = new TicketMessage;
    $messages = TicketMessage::model()->findAllByAttributes(array('ticket_id'=>$id),array('order'=>'dt ASC'));
    $agents = Agent::model()->getFullComboList(false);

    if ($ticket->status == 'CLOSED') $params['tiketClosed']=true;
    if ($ticket->whom == loggedAgentId()) $params['closeMessage']=true;
    $agent = Agent::model()->findByPk($ticket->agent_id);
    if (loggedAgentId()==$agent->parent_id || loggedAgentId()==1) $params['priseMessage']=true;

    $this->render('view',array('model'=>$model,'messages'=>$messages,'agents'=>$agents,'params'=>$params));
  }

  public function actionAnswer($ticket, $type) {
    if (isset($_POST['TicketMessage'])) {
      $model = new TicketMessage;
      $model->setAttributes($_POST['TicketMessage']);
      $this->performAjaxValidation($model, 'add-message');

      $model->agent_id = loggedAgentId();
      $model->ticket_id = $ticket;
      $model->dt = new EDateTime();

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

    $this->redirect($this->createUrl('view',array('id'=>$ticket, 'type'=>$type)));
  }

  public function actionClose($ticket='', $type='') {
    $model = Ticket::model()->findByPk($ticket);
    if ($model->whom == loggedAgentId()) {
      if (isset($_POST['PriseMessage']['prise'])) {
        $prise = $_POST['PriseMessage']['prise'];
        $Act = new Act;
        $Act->agent_id = $model->agent_id;
        $Act->dt = new EDateTime();
        $Act->sum = $prise;
        $Act->type= Act::TYPE_NORMAL;

        $transaction=Yii::app()->db->beginTransaction();
        try {
          $Act->save(false);
          $Act->agent->recalcBalance();
          $Act->agent->save();
          $transaction->commit();
        } catch (CDbException $e) {
          $transaction->rollback();
        }
      } else $prise = 0;

      try {
        $model->status = 'CLOSED';
        $model->prise = $prise;
        $model->update();
      } catch (CDbException $e) {}
    }
    if ($type=="inbox") $url="inbox"; elseif ($type=="outbox") $url="outbox"; else $url="view";
    $this->redirect($this->createUrl($url));
  }
}