<?php

Yii::import('application.models._base.BaseNumber');

class Number extends BaseNumber
{
    const STATUS_NORMAL='NORMAL';
    const STATUS_WARNING='WARNING';

    public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}