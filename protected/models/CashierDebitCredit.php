<?php

Yii::import('application.models._base.BaseCashierDebitCredit');

class CashierDebitCredit extends BaseCashierDebitCredit
{
    const TYPE_NUMBER_SELL='NUMBER_SELL';
    const TYPE_NUMBER_RESTORE='NUMBER_RESTORE';
    const TYPE_NUMBER_RESTORE_REJECT='NUMBER_RESTORE_REJECT';
    const TYPE_COLLECTION='COLLECTION';
    const TYPE_OTHER='OTHER';

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}