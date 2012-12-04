<?php

Yii::import('application.models._base.BaseTariff');

class Tariff extends BaseTariff
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}