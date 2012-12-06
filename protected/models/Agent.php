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
            'defaultOrder' => 'surname,name,middle_name'
        ));
        return $data_provider;
    }

    public static function getComboList() {
        $agents=Yii::app()->db->createCommand("select id,name,surname,middle_name from ".
            self::model()->tableName()." order by surname,name,middle_name")->queryAll();

        $data=array();
        foreach($agents as $v) {
            $data[$v['id']]=$v['surname'].' '.$v['name'].' '.$v['middle_name'];
        }

        return $data;
    }
}