<?php

Yii::import('application.models._base.BaseBonusReportNumber');

class BonusReportNumber extends BaseBonusReportNumber
{
    const STATUS_OK='OK';
    const STATUS_TURNOVER_ZERO='TURNOVER_ZERO';
    const STATUS_NUMBER_MISSING='NUMBER_MISSING';

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public static function getStatusDropDownList($items=array()) {
        $labels=self::getStatusLabels();
        return array_merge($items,$labels);
    }

    public static function getStatusLabels() {
        static $labels;

        if (!$labels) {
            $labels=array(
                self::STATUS_OK=>Yii::t('app','STATUS_OK'),
                self::STATUS_TURNOVER_ZERO=>Yii::t('app','STATUS_TURNOVER_ZERO'),
                self::STATUS_NUMBER_MISSING=>Yii::t('app','STATUS_NUMBER_MISSING'),
            );
        }

        return $labels;
    }

    public function getStatusLabel($status) {
        $labels=self::getStatusLabels();
        return $labels[$status];
    }



    public function rules()
    {
        $rules=parent::rules();
        $this->addRules($rules,array(
            array('turnover, rate, sum', 'numerical', 'integerOnly' => false, 'min' => 0),
        ));
        $this->delRules($rules,array(
            array('turnover, sum, status', 'length'),
        ));

        return $rules;
    }

}