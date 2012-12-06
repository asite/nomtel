<?php

Yii::import('application.models._base.BaseOperator');

class Operator extends BaseOperator
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function search() {
        $data_provider = parent::search();

        $data_provider->setSort(array(
            'defaultOrder' => 'title asc'
        ));
        return $data_provider;
    }
}