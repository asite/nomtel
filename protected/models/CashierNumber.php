<?php

Yii::import('application.models._base.BaseCashierNumber');

class CashierNumber extends BaseCashierNumber
{
    const TYPE_SELL='SELL';
    const TYPE_RESTORE='RESTORE';

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}