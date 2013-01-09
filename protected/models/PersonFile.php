<?php

Yii::import('application.models._base.BasePersonFile');

class PersonFile extends BasePersonFile
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}