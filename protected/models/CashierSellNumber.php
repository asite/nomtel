<?php

Yii::import('application.models._base.BaseCashierSellNumber');

class CashierSellNumber extends BaseCashierSellNumber
{
    const TYPE_CLIENT='CLIENT';
    const TYPE_AGENT='AGENT';

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public static function getTypeDropDownList($items=array()) {
        $labels=self::getTypeLabels();
        return array_merge($items,$labels);
    }

    public static function getTypeLabels() {
        static $labels;

        if (!$labels) {
            $labels=array(
                self::TYPE_CLIENT=>'Розничная',
                self::TYPE_AGENT=>'Aгенту'
            );
        }

        return $labels;
    }

    public function getTypeLabel($status) {
        $labels=self::getTypeLabels();
        return $labels[$status];
    }

}