<?php

Yii::import('application.models._base.BaseSmsLog');

class SmsLog extends BaseSmsLog
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}