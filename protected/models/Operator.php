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

    public static function getComboList() {
        $agents=Yii::app()->db->createCommand("select id,title from ".
            self::model()->tableName()." order by title")->queryAll();

        $data=array();
        foreach($agents as $v) {
            $data[$v['id']]=$v['title'];
        }

        return $data;
    }

    public static function getTextComboList() {
        $agents=Yii::app()->db->createCommand("select title from ".
            self::model()->tableName()." order by title")->queryAll();

        $data=array();
        foreach($agents as $v) {
            $data[$v['title']]=$v['title'];
        }

        return $data;
    }
}