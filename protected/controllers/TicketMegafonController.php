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
                }

                if (isset($_POST['refuse'])) {
                    $ticket->support_operator_id=loggedSupportOperatorId();
                    $ticket->status=Ticket::STATUS_REFUSED_BY_MEGAFON;
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
        }

        $ticketHistory=new TicketHistory();
        $ticketHistory->ticket_id=$ticket->id;
        $data['ticketHistory']=$ticketHistory;

        $this->render('detail',$data);
    }
}
