<?php

Yii::import('application.models._base.BaseNumberHistory');

class NumberHistory extends BaseNumberHistory
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function addHistoryNumber($number_id,$who,$comment) {
        $model = new NumberHistory;
        $model->number_id = $number_id;
        $model->dt = new EDateTime();
        $model->who = $who;
        $model->comment = $comment;
        $model->save();
    }
}