<?php

Yii::import('application.models._base.BasePerson');

class Person extends BasePerson
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}