<?php

Yii::import('application.models._base.BaseSim');

class Sim extends BaseSim
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}