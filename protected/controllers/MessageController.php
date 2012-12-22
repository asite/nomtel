<?php

class MessageController extends Controller {

  protected function performAjaxValidation($models, $id){
    if(isset($_POST['ajax']) && $_POST['ajax']===$id) {
      echo CActiveForm::validate($models);
      Yii::app()->end();
    }
  }

  public function actionAgentUp() {
    $agent = Agent::model()->findByPk(Yii::app()->user->getId());
    $criteria = new CDbCriteria();
    $criteria->condition = "(whom".($agent->parent_id?'=:whom':' IS :whom')." AND agent_id=:agent_id) OR whom=:agent_id";
    $criteria->params = array(
      ':whom' => $agent->parent_id,
      ':agent_id' => Yii::app()->user->getId()
    );
    //print_r($criteria); exit;
    $criteria->order = 'status DESC, date DESC';
    $dataProvider = new CActiveDataProvider('Ticket', array('criteria' => $criteria));

    $this->render('agentUp',array('dataProvider'=>$dataProvider));
  }

  public function actionAgentDown() {
    $agent = Agent::model()->findByPk(Yii::app()->user->getId());
    $criteria = new CDbCriteria();
    $criteria->condition = "(whom".($agent->parent_id?'=:whom':' IS NOT :whom').") AND agent_id=:agent_id";
    $criteria->params = array(
      ':whom' => $agent->parent_id,
      ':agent_id' => Yii::app()->user->getId()
    );
    //print_r($criteria); exit;
    $criteria->order = 'status DESC, date DESC';
    $dataProvider = new CActiveDataProvider('Ticket', array('criteria' => $criteria));

    $this->render('agentDown',array('dataProvider'=>$dataProvider));
  }

  public function actionCreateTicket($to) {
    $model = new Ticket;
    $model->ticketMessages = new TicketMessage;

    switch ($to) {
      case 'agentUp':
        $agent = Agent::model()->findByPk(Yii::app()->user->getId());
        $whom = $agent->parent_id;
        break;
      case 'agentDown':
        $agent = Agent::model()->findByAttributes(array('parent_id'=>Yii::app()->user->getId()));
        $whom = $agent->id;
        break;
    }

    if (isset($_POST['Ticket'])) {
      $this->performAjaxValidation(array($model,$model->ticketMessages), 'create-ticket');
      $model->attributes = $_POST['Ticket'];
      $model->date = date('Y-m-d H:i:s');
      $model->agent_id = Yii::app()->user->getId();
      $model->whom = $whom;

      $model->ticketMessages->attributes = $_POST['TicketMessage'];
      $model->ticketMessages->agent_id = $model->agent_id;
      $model->ticketMessages->date = $model->date;

      $transaction=Yii::app()->db->beginTransaction();
        try{
          $model->save(false);
          $model->ticketMessages->ticket_id = $model->id;
          $model->ticketMessages->save();
          $transaction->commit();
          Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Сообщение отправлено.');
          $this->refresh();
        }catch( Exception $e ){
          $transaction->rollback();
          throw $e;
        }
    }

    $this->render('createTicket', array('model'=>$model));
  }
}