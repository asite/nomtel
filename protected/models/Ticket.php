<?php

Yii::import('application.models._base.BaseTicket');

class Ticket extends BaseTicket
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}