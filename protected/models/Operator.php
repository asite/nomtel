<?php

Yii::import('application.models._base.BaseOperator');

class Operator extends BaseOperator
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}