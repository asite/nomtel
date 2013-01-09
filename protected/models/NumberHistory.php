<?php

Yii::import('application.models._base.BaseNumberHistory');

class NumberHistory extends BaseNumberHistory
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}