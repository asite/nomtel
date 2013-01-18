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

    public function addNumber($sim) {
        if ($sim->number) {
            $number = self::model()->findByAttributes(array('number'=>$sim->number));
            if (empty($number)) {
                $model = new Number;
                $model->sim_id = $sim->id;
                $model->number = $sim->number;
                $model->personal_account = $sim->personal_account;
                $model->status = self::STATUS_FREE;
                $model->save();
                NumberHistory::addHistoryNumber($model->id,'База','SIM {Sim:'.$sim->id.'} добавлена в базу');
            }
        }
    }
}