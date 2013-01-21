<?php

Yii::import('application.models._base.BaseNumberHistory');

class NumberHistory extends BaseNumberHistory
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function addHistoryNumber($number_id,$comment,$who=null) {
        if (!isset($who)) {
            switch (Yii::app()->user->role) {
                case 'admin':
                    $who='База';
                    break;
                case 'agent':
                    $who='Агент {Agent:'.loggedAgentId().'}';
                    break;
                case 'support':
                    $who='Техподдержка {SupportOperator:'.loggedSupportOperatorId().'}';
            }
        }
        $model = new NumberHistory;
        $model->number_id = $number_id;
        $model->dt = new EDateTime();
        $model->who = $who;
        $model->comment = $comment;
        $model->save();
    }

    public function search()
    {
        $data_provider = parent::search();

        $data_provider->setSort(array(
            'defaultOrder' => 'dt DESC'
        ));
        return $data_provider;
    }
}