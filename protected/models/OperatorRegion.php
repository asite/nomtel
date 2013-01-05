<?php

Yii::import('application.models._base.BaseOperatorRegion');

class OperatorRegion extends BaseOperatorRegion
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function getDropDownList() {
      $data = Yii::app()->db->createCommand("select id,title,operator_id from " .self::model()->tableName() . " order by title")->queryAll();
      $result = array(Operator::OPERATOR_BEELINE_ID => array('' => 'Выбор региона'), Operator::OPERATOR_MEGAFON_ID => array('' => 'Выбор региона'));
      foreach ($data as $v) {
        $result[$v['operator_id']][$v['id']] = $v['title'];
      }
      return $result;
    }
}