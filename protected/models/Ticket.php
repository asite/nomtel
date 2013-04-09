<?php

Yii::import('application.models._base.BaseTicket');

class Ticket extends BaseTicket
{
    const ITEMS_PER_PAGE=20;

    const STATUS_NEW='NEW';
    const STATUS_IN_WORK_MEGAFON='IN_WORK_MEGAFON';
    const STATUS_IN_WORK_OPERATOR='IN_WORK_OPERATOR';
    const STATUS_REFUSED_BY_MEGAFON='REFUSED_BY_MEGAFON';
    const STATUS_REFUSED_BY_ADMIN='REFUSED_BY_ADMIN';
    const STATUS_REFUSED_BY_OPERATOR='REFUSED_BY_OPERATOR';
    const STATUS_FOR_REVIEW='FOR_REVIEW';
    const STATUS_DONE='DONE';
    const STATUS_REFUSED='REFUSED';

    const TYPE_NORMAL='NORMAL';
    const TYPE_OCR_DOCS='OCR_DOCS';

    const MEGAFON_STATUS_DONE='DONE';
    const MEGAFON_STATUS_REFUSED='REFUSED';
    
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public static function getStatusDropDownList($items=array()) {
        $labels=self::getStatusLabels();
        return array_merge($items,$labels);
    }

    public static function getStatusLabels() {
        static $labels;

        if (!$labels) {
            $labels=array(
                self::STATUS_NEW=>Yii::t('app','NEW'),
                self::STATUS_IN_WORK_MEGAFON=>Yii::t('app','IN_WORK_MEGAFON'),
                self::STATUS_IN_WORK_OPERATOR=>Yii::t('app','IN_WORK_OPERATOR'),
                self::STATUS_REFUSED_BY_MEGAFON=>Yii::t('app','REFUSED_BY_MEGAFON'),
                self::STATUS_REFUSED_BY_ADMIN=>Yii::t('app','REFUSED_BY_ADMIN'),
                self::STATUS_REFUSED_BY_OPERATOR=>Yii::t('app','REFUSED_BY_OPERATOR'),
                self::STATUS_FOR_REVIEW=>Yii::t('app','FOR_REVIEW'),
                self::STATUS_DONE=>Yii::t('app','DONE'),
                self::STATUS_REFUSED=>Yii::t('app','REFUSED')
            );
        }

        return $labels;
    }

    public static function getStatusLabel($status) {
        $labels=self::getStatusLabels();
        return $labels[$status];
    }

    public static function getMegafonStatusDropDownList($items=array()) {
        $labels=self::getMegafonStatusLabels();
        return array_merge($items,$labels);
    }

    public static function getMegafonStatusLabels() {
        static $labels;

        if (!$labels) {
            $labels=array(
                self::MEGAFON_STATUS_DONE=>Yii::t('app','DONE'),
                self::MEGAFON_STATUS_REFUSED=>Yii::t('app','REFUSED')
            );
        }

        return $labels;
    }

    public static function getMegafonStatusLabel($status) {
        $labels=self::getMegafonStatusLabels();
        return $labels[$status];
    }
    
    public static function getStatusPOLabel($status) {
        switch ($status) {
            case self::STATUS_DONE:
                return Yii::t('app','DONE');
                break;
            case self::STATUS_REFUSED:
                return Yii::t('app','REFUSED');
                break;
            default:
                return Yii::t('app','IN_PROCESSING');
                break;
        }
    }

    public function addHistory($comment) {
        $history=new TicketHistory();
        $history->ticket_id=$this->id;
        $history->dt=new EDateTime();
        if (loggedSupportOperatorId()) $history->support_operator_id=loggedSupportOperatorId();
        if (loggedAgentId()) $history->agent_id=loggedAgentId();
        $history->comment=$comment;
        $history->status=$this->status;

        if ($this->status==Ticket::STATUS_DONE) {
            $message = "Ваше обращение #".$this->id." обработано (выполнено).";
            Sms::send($this->number->number,$message);
        }

        if ($this->status==Ticket::STATUS_REFUSED) {
            $message = "Ваше обращение #".$this->id." обработано (отказано).";
            Sms::send($this->number->number,$message);
        }

        $history->save();
    }

    public static function addMessage($idNumber, $message) {
        $number=Number::model()->findByPk($idNumber);

        $criteria = new CDbCriteria();
        $criteria->addCondition('parent_id = :parent_id');
        $criteria->addCondition('agent_id is null');
        $criteria->params = array(':parent_id' => $number->sim_id);
        $sim = Sim::model()->find($criteria);

        $ticket = new Ticket;
        $ticket->number_id = $idNumber;
        $ticket->sim_id = $sim->id;
        $ticket->agent_id = $sim->parent_agent_id;
        $ticket->status = self::STATUS_NEW;
        $ticket->dt = new EDateTime();
        $ticket->text = $message;
        $ticket->save();
        $ticket->addHistory($message);

        return $ticket->id;
    }

    public function __toString() {
        return 'Обращение #'.$this->id.' от '.$this->dt->format('d.m.Y');
    }

    public function beforeValidate() {
        if (!$this->created_by) $this->created_by=NumberHistory::getDefaultWho();
        return parent::beforeValidate();
    }

    public function rules() {
        return array_merge(parent::rules(),array(
            array('internal','required','on'=>'internalRequired','message'=>'Заполните пожалуйста это поле'),
            array('response','required','on'=>'responseRequired','message'=>'Заполните пожалуйста это поле')
        ));
    }
}
