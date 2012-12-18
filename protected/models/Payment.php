<?php

Yii::import('application.models._base.BasePayment');

class Payment extends BasePayment
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function rules() {
        return array_merge(parent::rules(),array(
            array('sum', 'numerical','integerOnly'=>true,'min'=>1),
        ));
    }

}