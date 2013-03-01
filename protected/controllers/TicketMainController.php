<?php

class TicketMainController extends BaseGxController {
    private $availableStatuses=array(
        Ticket::STATUS_REFUSED_BY_ADMIN,
        Ticket::STATUS_REFUSED_BY_OPERATOR,
        Ticket::STATUS_REFUSED_BY_MEGAFON,
        Ticket::STATUS_FOR_REVIEW,
    );

    public function additionalAccessRules() {
        return array(
            array('allow', 'roles' => array('supportMain')),
        );
    }

    public function getStatusDropDownList($items) {
        $res=array();
        foreach(Ticket::getStatusDropDownList($items) as $k=>$v)
            if (isset($items[$k]) || in_array($k,$this->availableStatuses)) $res[$k]=$v;

        return $res;
    }

    public function actionIndex() {
        $data=array();

        $criteria=new CDbCriteria();
        $criteria->addInCondition('t.status',$this->availableStatuses);

        list($data['dataProvider'],$data['model'])=TicketSearch::getSqlDataProvider($criteria);

        $this->render('index',$data);
    }

    public function actionDetail($id) {
        $data=array();

        $ticket=$this->loadModel($id,'Ticket');
        $data['ticket']=$ticket;

        if (isset($_POST['Ticket'])) {
            $ticket->setScenario('internalRequired');
            $ticket->setAttributes($_POST['Ticket']);

            if ($ticket->validate()) {
                $ticket->setScenario('');

                $ticket->support_operator_id=null;

                $comment=$ticket->response;

                if (isset($_POST['accept'])) {
                    $ticket->status=Ticket::STATUS_DONE;
                }

                if (isset($_POST['refuse'])) {
                    $ticket->status=Ticket::STATUS_REFUSED;
                }

                if (isset($_POST['toAdmin'])) {
                    $ticket->status=Ticket::STATUS_NEW;
                }

                $trx=Yii::app()->db->beginTransaction();

                $ticket->addHistory($comment);
                $ticket->save();

                $trx->commit();

                $this->redirect(array('index'));
            }
        }

        $ticketHistory=new TicketHistory();
        $ticketHistory->ticket_id=$ticket->id;
        $data['ticketHistory']=$ticketHistory;

        $this->render('detail',$data);
    }
}
