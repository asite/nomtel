<?php

Yii::import('application.models._base.BaseNumber');

class Number extends BaseNumber
{
    const STATUS_UNKNOWN = 'UNKNOWN';
    const STATUS_FREE = 'FREE';
    const STATUS_CONNECTED = 'CONNECTED';

    public static function model($className = __CLASS__)
    {
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
                self::STATUS_UNKNOWN=>Yii::t('app','UNKNOWN'),
                self::STATUS_FREE=>Yii::t('app','FREE'),
                self::STATUS_CONNECTED=>Yii::t('app','CONNECTED')
            );
        }

        return $labels;
    }

    public function getStatusLabel($status) {
        $labels=self::getStatusLabels();
        return $labels[$status];
    }

    public static function getWarningDropDownList($items=array()) {
        $labels=self::getWarningLabels();
        return array_merge($items,$labels);
    }

    public static function getWarningLabels() {
        static $labels;

        if (!$labels) {
            $labels=array(
                '0'=>Yii::t('app','No'),
                '1'=>Yii::t('app','Yes'),
            );
        }

        return $labels;
    }

    public function getWarningLabel($status) {
        $labels=self::getWarningLabels();
        return $labels[$status];
    }
    
    public function addNumber($sim) {
        if ($sim->number) {
            $number = self::model()->findByAttributes(array('number'=>$sim->number));
            if (empty($number)) {
                $number = new Number;
                $number->sim_id = $sim->id;
                $number->number = $sim->number;
                $number->personal_account = $sim->personal_account;
                $number->status = self::STATUS_FREE;
                $number->save();
            }
            if ($number->status!=self::STATUS_FREE) {
                $number->status=self::STATUS_FREE;
                $number->save();
            }
            NumberHistory::addHistoryNumber($number->id,'SIM {Sim:'.$sim->id.'} добавлена в базу');
        }
    }
}