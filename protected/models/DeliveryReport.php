<?php

Yii::import('application.models._base.BaseDeliveryReport');

class DeliveryReport extends BaseDeliveryReport
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function __toString() {
        return '№'.$this->id.' '.Yii::t('app','from').' '.$this->dt;
    }
}