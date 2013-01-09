<?php

Yii::import('application.models._base.BaseFile');

class File extends BaseFile
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}