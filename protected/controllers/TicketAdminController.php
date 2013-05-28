<?php

class TicketAdminController extends BaseGxController {

    public function additionalAccessRules() {
        return array(
            array('allow', 'roles' => array('supportAdmin')),
        );
    }

    public function actionIndex() {
        $data=array();

        $criteria=new CDbCriteria();
        if (!isset($_GET['TicketSearch']['status'])) $_GET['TicketSearch']['status']=Ticket::STATUS_NEW;

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

                $comment=$ticket->internal;

                $ticket->support_operator_id=null;

                if (isset($_POST['done'])) {
                    $ticket->response=$ticket->internal;
                    $ticket->internal=null;
                    $ticket->status=Ticket::STATUS_DONE;
                }

                if (isset($_POST['toOperators'])) {
                    $ticket->status=Ticket::STATUS_IN_WORK_OPERATOR;
                }

                if (isset($_POST['reject'])) {
                    $ticket->status=Ticket::STATUS_REFUSED;
                }

                if (isset($_POST['toMegafon'])) {
                    $ticket->status=Ticket::STATUS_IN_WORK_MEGAFON;
                    $ticket->sendMegafonNotification();
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
