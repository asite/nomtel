<?php

Yii::import('application.models._base.BaseMegafonAppRestore');

class MegafonAppRestore extends BaseMegafonAppRestore
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public static function getCurrent() {
        $now=new EDateTime();
        $now->setTimezone(new DateTimeZone('Europe/Moscow'));
        if ($now->format('H')<4) $now->modify('-1 DAY');

        $model=self::model()->findByAttributes(array('dt'=>$now->toMysqlDate()));

        if (!$model) {
            $model=new MegafonAppRestore();
            $model->dt=$now->toMysqlDate();
            $model->save();
        }

        return $model;
    }
}