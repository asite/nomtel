<?php

Yii::import('application.models._base.BaseCompany');

class Company extends BaseCompany
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function getDropDownList() {
      $data = Yii::app()->db->createCommand("select id,title from " .self::model()->tableName() . " order by title")->queryAll();
      $result = array('' => 'Выбор компании');
      foreach ($data as $v) {
        $result[$v['id']] = $v['title'];
      }
      return $result;
    }
}