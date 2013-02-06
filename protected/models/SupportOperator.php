<?php

Yii::import('application.models._base.BaseSupportOperator');

class SupportOperator extends BaseSupportOperator
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function __toString()
    {
        return $this->surname . ' ' . $this->name . ' ' . $this->middle_name;
    }
}