<?php

Yii::import('application.models._base.BaseTariff');

class Tariff extends BaseTariff
{
    const TARIFF_TERRITORY_ID=3;

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
        $agents = Yii::app()->db->createCommand("select id,title from ".self::model()->tableName()." order by title")->queryAll();

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

    public static function getDropDownList() {
      $data = Yii::app()->db->createCommand("select id,title,operator_id from " .self::model()->tableName() . " order by title")->queryAll();
      $result = array(Operator::OPERATOR_BEELINE_ID => array('' => 'Выбор тарифа'), Operator::OPERATOR_MEGAFON_ID => array('' => 'Выбор тарифа'));
      foreach ($data as $v) {
        $result[$v['operator_id']][$v['id']] = $v['title'];
      }
      return $result;
    }

    public function rules()
    {
        $rules=parent::rules();
        $this->addRules($rules,array(
            array('price_agent_sim, price_license_fee', 'numerical', 'integerOnly' => false, 'min' => 0),
        ));
        $this->delRules($rules,array(
            array('price_agent_sim, price_license_fee', 'length'),
        ));

        return $rules;
    }
}