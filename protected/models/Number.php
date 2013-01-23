<?php

Yii::import('application.models._base.BaseNumber');

class Number extends BaseNumber
{
    const STATUS_UNKNOWN = 'UNKNOWN';
    const STATUS_FREE = 'FREE';
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_BLOCKED = 'BLOCKED';

    const BALANCE_STATUS_NORMAL = 'NORMAL';
    const BALANCE_STATUS_POSITIVE_STATIC = 'POSITIVE_STATIC';
    const BALANCE_STATUS_NEGATIVE_STATIC = 'NEGATIVE_STATIC';
    const BALANCE_STATUS_POSITIVE_DYNAMIC = 'POSITIVE_DYNAMIC';
    const BALANCE_STATUS_NEGATIVE_DYNAMIC = 'NEGATIVE_DYNAMIC';
    const BALANCE_STATUS_NEW = 'NEW';
    const BALANCE_STATUS_MISSING = 'MISSING';

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
                self::STATUS_ACTIVE=>Yii::t('app','ACTIVE'),
                self::STATUS_BLOCKED=>Yii::t('app','BLOCKED')
            );
        }

        return $labels;
    }

    public function getStatusLabel($status) {
        $labels=self::getStatusLabels();
        return $labels[$status];
    }

    public static function getBalanceStatusDropDownList($items=array()) {
        $labels=self::getBalanceStatusLabels();
        return array_merge($items,$labels);
    }

    public static function getBalanceStatusLabels() {
        static $labels;

        if (!$labels) {
            $labels=array(
                self::BALANCE_STATUS_NORMAL => Yii::t('app','BALANCE_STATUS_NORMAL'),
                self::BALANCE_STATUS_POSITIVE_STATIC => Yii::t('app','BALANCE_STATUS_POSITIVE_STATIC'),
                self::BALANCE_STATUS_NEGATIVE_STATIC => Yii::t('app','BALANCE_STATUS_NEGATIVE_STATIC'),
                self::BALANCE_STATUS_POSITIVE_DYNAMIC => Yii::t('app','BALANCE_STATUS_POSITIVE_DYNAMIC'),
                self::BALANCE_STATUS_NEGATIVE_DYNAMIC => Yii::t('app','BALANCE_STATUS_NEGATIVE_DYNAMIC'),
                self::BALANCE_STATUS_NEW => Yii::t('app','BALANCE_STATUS_NEW'),
                self::BALANCE_STATUS_MISSING => Yii::t('app','BALANCE_STATUS_MISSING')
            );
        }

        return $labels;
    }

    public function getBalanceStatusLabel($status) {
        $labels=self::getBalanceStatusLabels();
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