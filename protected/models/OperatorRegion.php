<?php

Yii::import('application.models._base.BaseOperatorRegion');

class OperatorRegion extends BaseOperatorRegion
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function getComboList($data = array())
    {
        $regions = Yii::app()->db->createCommand("select id,title from " .
            self::model()->tableName() . " order by title")->queryAll();

        foreach ($regions as $v) {
            $data[$v['id']] = $v['title'];
        }

        return $data;
    }

    public static function getDropDownList() {
      $data = Yii::app()->db->createCommand("select id,title,operator_id from " .self::model()->tableName() . " order by title")->queryAll();
      $result = array(Operator::OPERATOR_BEELINE_ID => array('' => 'Выбор региона'), Operator::OPERATOR_MEGAFON_ID => array('' => 'Выбор региона'));
      foreach ($data as $v) {
        $result[$v['operator_id']][$v['id']] = $v['title'];
      }
      return $result;
    }

    public static function getGroupedDropDownList() {
        $data = Yii::app()->db->createCommand("select id,title,operator_id from " .self::model()->tableName() . " order by title")->queryAll();
        $result = array(Operator::OPERATOR_BEELINE_ID => array(), Operator::OPERATOR_MEGAFON_ID => array());
        foreach ($data as $v) {
            $result[$v['operator_id']][$v['id']] = $v['title'];
        }

        $result2=array();
        foreach($result as $operator_id=>$regions) {
            $operator=Operator::model()->findByPk($operator_id);
            foreach($regions as $region_id=>$region)
                $result2[$region_id]=$operator->title.' - '.$region;
        }

        return $result2;
    }
}