<?php

class TicketMegafonController extends BaseGxController {

    public function additionalAccessRules() {
        return array(
           // array('disallow', 'actions'=>array('indexAdmin','detailAdmin'),'roles' => array('supportMegafon')),
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

    public function actionIndexAdmin() {
        $data=array();

        $criteria=new CDbCriteria();
        $criteria->addCondition('t.status=:in_work_megafon or t.megafon_status is not null');
        $criteria->params[':in_work_megafon']=Ticket::STATUS_IN_WORK_MEGAFON;

        list($data['dataProvider'],$data['model'])=TicketSearch::getSqlDataProvider($criteria);
        $data['dataProvider']->sort->defaultOrder='t.dt DESC';

        $this->render('indexAdmin',$data);
    }

    public function actionDetailAdmin($id) {
        $data=array();

        $ticket=$this->loadModel($id,'Ticket');
        $data['ticket']=$ticket;

        $ticketHistory=new TicketHistory();
        $ticketHistory->ticket_id=$ticket->id;
        $data['ticketHistory']=$ticketHistory;

        $this->render('detailAdmin',$data);
    }

    public function actionDetail($id) {
        $data=array();

        $ticket=$this->loadModel($id,'Ticket');
        if (isset($_POST['refuse']))
            $ticket->setScenario('responseRequired');

        $this->performAjaxValidation($ticket);

        $data['ticket']=$ticket;

        if (isset($_POST['Ticket'])) {
            $ticket->setAttributes($_POST['Ticket']);

            if ($ticket->validate()) {
                $ticket->setScenario('');

                $comment=$ticket->response;

                if (isset($_POST['accept'])) {
                    $ticket->support_operator_id=loggedSupportOperatorId();
                    $ticket->status=Ticket::STATUS_FOR_REVIEW;
                    $ticket->megafon_status=Ticket::MEGAFON_STATUS_DONE;

                    $cashierNumber=CashierNumber::model()->findByAttributes(array('ticket_id'=>$ticket->id));
                    if ($cashierNumber) {
                        $cashierNumber->confirmed=1;
                        $cashierNumber->save();
                    }
                }

                if (isset($_POST['refuse'])) {
                    $ticket->support_operator_id=loggedSupportOperatorId();
                    $ticket->status=Ticket::STATUS_REFUSED_BY_MEGAFON;
                    $ticket->megafon_status=Ticket::MEGAFON_STATUS_REFUSED;
                }

                $trx=Yii::app()->db->beginTransaction();

                $ticket->addHistory($comment);
                $ticket->save();

                $trx->commit();

                if (isset($_POST['download'])) {
                    $ticket=$this->loadModel($id,'Ticket');

                    $number=$ticket->id;
                    $date=new EDateTime();

                    $data=array(
                        'number'=>$number,
                        'text'=>$ticket->internal,
                        'day'=>$date->format('d'),
                        'month'=>Yii::app()->dateFormatter->format('MMMM',$date->getTimestamp()),
                        'year'=>$date->format('Y'),
                    );

                    $generator=new DocumentGenerator();
                    $generator->generate('megafon_statement.docx','megafon_'.$number.'_'.date('Ymd').'.docx',$data);
                }

                $this->redirect(array('index'));
            }
            var_dump($ticket->getErrors());
            exit;
        }

        $ticketHistory=new TicketHistory();
        $ticketHistory->ticket_id=$ticket->id;
        $data['ticketHistory']=$ticketHistory;

        $this->render('detail',$data);
    }
}
