<?php

class TicketMegafonController extends BaseGxController {

    public function additionalAccessRules() {
        return array(
            array('allow', 'roles' => array('supportMegafon')),
        );
    }

    public function actionIndex() {
        $data=array();

        $criteria=new CDbCriteria();
        $criteria->compare('t.status',Ticket::STATUS_IN_WORK_MEGAFON);

        list($data['dataProvider'],$data['model'])=TicketSearch::getSqlDataProvider($criteria);

        $this->render('index',$data);
    }

    public function actionDetail($id) {
        $data=array();

        $ticket=$this->loadModel($id,'Ticket');
        $data['ticket']=$ticket;

        if (isset($_POST['Ticket'])) {
            $ticket->setScenario('responseRequired');
            $ticket->setAttributes($_POST['Ticket']);

            if ($ticket->validate() || true) {
                $ticket->setScenario('');

                $comment=$ticket->response;

                if (isset($_POST['accept'])) {
                    $ticket->status=Ticket::STATUS_FOR_REVIEW;
                }

                if (isset($_POST['refuse'])) {
                    $ticket->status=Ticket::STATUS_REFUSED_BY_MEGAFON;
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
