<?php

Yii::import('application.models._base.BaseAgent');

class Agent extends BaseAgent
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function __toString() {
        return $this->surname.' '.$this->name;
    }

    public function search() {
        $data_provider = parent::search();

        $data_provider->setSort(array(
            'defaultOrder' => 'surname asc'
        ));
        return $data_provider;
    }

}