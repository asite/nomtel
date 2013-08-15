<?php

Yii::import('application.models._base.BaseCashierDebitCredit');

class CashierDebitCredit extends BaseCashierDebitCredit
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}