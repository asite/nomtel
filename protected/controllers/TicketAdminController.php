<?php

class TicketAdminController extends BaseGxController {

    public function additionalAccessRules() {
        return array(
            array('allow', 'roles' => array('supportAdmin')),
        );
    }

    public function actionReject($id) {
        $ticket=$this->loadModel($id,'Ticket');

        $trx=Yii::app()->db->beginTransaction();

        $ticket->status=Ticket::STATUS_REFUSED_BY_ADMIN;
        $ticket->addHistory('');
        $ticket->save();

        $trx->commit();

        $this->redirect($this->createUrl('index'));
    }

    public function actionIndex() {
        $data=array();

        $criteria=new CDbCriteria();

        list($data['dataProvider'],$data['model'])=TicketSearch::getSqlDataProvider($criteria);

        $this->render('index',$data);
    }

    public function actionDetail($id) {
        $data=array();

        $ticket=$this->loadModel($id,'Ticket');
        $data['ticket']=$ticket;

        $ticketHistory=new TicketHistory();
        $ticketHistory->ticket_id=$ticket->id;
        $data['ticketHistory']=$ticketHistory;

        $this->render('detail',$data);
    }
}
