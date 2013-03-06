<?php

class TicketController extends BaseGxController {

    public function additionalAccessRules() {
        return array(
            array('allow', 'roles' => array('support')),
        );
    }

    public function actionIndexMy() {
        $this->actionIndex();
    }

    public function actionIndex() {
        $data=array();

        $criteria=new CDbCriteria();
        $criteria->compare('t.status',Ticket::STATUS_IN_WORK_OPERATOR);

        if ($this->action->id=='index') {
            $criteria->addCondition('t.support_operator_id is null');
        }
        if ($this->action->id=='indexMy') {
            $criteria->compare('t.support_operator_id',loggedSupportOperatorId());
        }

        list($data['dataProvider'],$data['model'])=TicketSearch::getSqlDataProvider($criteria);

        $this->render('index',$data);
    }

    public function actionAssignToMe($id) {
        $ticket=$this->loadModel($id,'Ticket');
        if ($ticket->support_operator_id) $this->redirect(array('index'));
        $ticket->support_operator_id=loggedSupportOperatorId();
        $ticket->save();
        $this->redirect(array('detail','id'=>$id));
    }

    public function actionDetail($id) {
        $data=array();

        $ticket=$this->loadModel($id,'Ticket');
        if ($ticket->support_operator_id!=loggedSupportOperatorId()) $this->redirect(array('index'));
        $data['ticket']=$ticket;

        if (isset($_POST['Ticket'])) {
            $ticket->setScenario('responseRequired');
            $ticket->setAttributes($_POST['Ticket']);

            if ($ticket->validate()) {
                $ticket->setScenario('');

                $comment=$ticket->response;

                if (isset($_POST['accept'])) {
                    $ticket->status=Ticket::STATUS_FOR_REVIEW;
                }

                if (isset($_POST['refuse'])) {
                    $ticket->status=Ticket::STATUS_REFUSED_BY_OPERATOR;
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
