<?php

Yii::import('application.models._base.BaseBlankSim');

class BlankSim extends BaseBlankSim
{
    const TYPE_NORMAL='NORMAL';
    const TYPE_MICRO='MICRO';
    const TYPE_NANO='NANO';
    
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
                self::TYPE_NORMAL=>Yii::t('app','BLANK_SIM_TYPE_NORMAL'),
                self::TYPE_MICRO=>Yii::t('app','BLANK_SIM_TYPE_MICRO'),
                self::TYPE_NANO=>Yii::t('app','BLANK_SIM_TYPE_NANO')
            );
        }

        return $labels;
    }

    public static function getTypeLabel($status) {
        $labels=self::getTypeLabels();
        return $labels[$status];
    }

    public function __toString() {
        return $this->icc;
    }
}