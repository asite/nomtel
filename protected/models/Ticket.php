<?php

Yii::import('application.models._base.BaseTicket');

class Ticket extends BaseTicket
{
    const STATUS_NEW='NEW';
    const STATUS_IN_WORK_MEGAFON='IN_WORK_MEGAFON';
    const STATUS_IN_WORK_OPERATOR='IN_WORK_OPERATOR';
    const STATUS_REFUSED_BY_MEGAFON='REFUSED_BY_MEGAFON';
    const STATUS_REFUSED_BY_ADMIN='REFUSED_BY_ADMIN';
    const STATUS_REFUSED_BY_OPERATOR='REFUSED_BY_OPERATOR';
    const STATUS_FOR_REVIEW='FOR_REVIEW';
    const STATUS_DONE='DONE';

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
                self::STATUS_NEW=>Yii::t('app','NEW'),
                self::STATUS_IN_WORK_MEGAFON=>Yii::t('app','IN_WORK_MEGAFON'),
                self::STATUS_IN_WORK_OPERATOR=>Yii::t('app','IN_WORK_OPERATOR'),
                self::STATUS_REFUSED_BY_MEGAFON=>Yii::t('app','REFUSED_BY_MEGAFON'),
                self::STATUS_REFUSED_BY_ADMIN=>Yii::t('app','REFUSED_BY_ADMIN'),
                self::STATUS_REFUSED_BY_OPERATOR=>Yii::t('app','REFUSED_BY_OPERATOR'),
                self::STATUS_FOR_REVIEW=>Yii::t('app','FOR_REVIEW'),
                self::STATUS_DONE=>Yii::t('app','DONE')
            );
        }

        return $labels;
    }

    public static function getStatusLabel($status) {
        $labels=self::getStatusLabels();
        return $labels[$status];
    }

}