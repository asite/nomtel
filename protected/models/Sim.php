<?php

Yii::import('application.models._base.BaseSim');

class Sim extends BaseSim
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getShortIcc()
    {
        if ($this->icc=='') return '';
        return '...' . substr($this->icc, -6);
    }

    public function getTotalNumberPrice($ids = '')
    {
        if (empty($ids) || !is_array($ids)) return 0;
        $criteria = new CDbCriteria;
        $criteria->select = 'sum(number_price) as number_price';
        if (is_array($ids)) $criteria->addInCondition('id', $ids);
        return $this->find($criteria)->getAttribute('number_price');
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('t.id', $this->id, true);
        $criteria->compare('t.act_id', $this->act_id);
        $criteria->compare('t.personal_account', $this->personal_account, true);

        if ($this->number != Yii::t('app', 'WITHOUT NUMBER'))
            $criteria->compare('t.number', $this->number, true);
        else
            $criteria->addCondition("(t.number='' or t.number is null)");

        if ($this->agent_id !== '0')
            $criteria->compare('t.agent_id', $this->agent_id);
        else
            $criteria->addCondition("t.agent_id is null");

        $criteria->compare('t.number_price', $this->number_price);
        $criteria->compare('t.icc', $this->icc, true);
        $criteria->compare('t.operator_id', $this->operator_id);
        $criteria->compare('t.tariff_id', $this->tariff_id);

        $dataProvider = new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));

        $dataProvider->pagination->pageSize = self::ITEMS_PER_PAGE;
        return $dataProvider;
    }

    public function relations() {
        return array_merge(parent::relations(),array(
            'numberObjectBySimId' => array(self::HAS_ONE, 'Number', array('sim_id'=>'parent_id')),
        ));
    }

    public function __toString()
    {
        return $this->shortIcc;
    }

    public function rules()
    {
        $rules=parent::rules();
        $this->addRules($rules,array(
            array('number_price, sim_price', 'numerical', 'integerOnly' => false, 'min' => 0),
        ));
        $this->delRules($rules,array(
            array('number_price, sim_price', 'length'),
        ));

        return $rules;
    }
}