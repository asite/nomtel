<?php

Yii::import('application.models._base.BaseCashierCollection');

class CashierCollection extends BaseCashierCollection
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function attributeLabels() {
        $labels=parent::attributeLabels();
        $labels['collector_support_operator_id']='Инкассатор';

        return $labels;
    }
}