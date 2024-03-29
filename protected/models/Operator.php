<?php

Yii::import('application.models._base.BaseOperator');

class Operator extends BaseOperator
{
    const OPERATOR_BEELINE_ID = 1;
    const OPERATOR_MEGAFON_ID = 2;


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function search()
    {
        $data_provider = parent::search();

        $data_provider->setSort(array(
            'defaultOrder' => 'title asc'
        ));
        return $data_provider;
    }

    public static function getComboList($data = array())
    {
        $agents = Yii::app()->db->createCommand("select id,title from " .
            self::model()->tableName() . " order by title")->queryAll();

        foreach ($agents as $v) {
            $data[$v['id']] = $v['title'];
        }

        return $data;
    }

    public static function getTextComboList()
    {
        $agents = Yii::app()->db->createCommand("select title from " .
            self::model()->tableName() . " order by title")->queryAll();

        $data = array();
        foreach ($agents as $v) {
            $data[$v['title']] = $v['title'];
        }

        return $data;
    }
}