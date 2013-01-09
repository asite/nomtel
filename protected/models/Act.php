<?php

Yii::import('application.models._base.BaseAct');

class Act extends BaseAct
{
    const TYPE_NORMAL = 'NORMAL';
    const TYPE_SIM = 'SIM';

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function __toString()
    {
        return '№' . $this->id . ' ' . Yii::t('app', 'from') . ' ' . $this->dt;
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->alias = 'act';
        $criteria->compare('act.id', $this->id);
        $criteria->compare('act.agent_id', $this->agent_id);
        $criteria->compare('act.dt', $this->dt, true);
        $criteria->compare('act.sum', $this->sum);

        $dataProvider = new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));

        $dataProvider->pagination->pageSize = self::ITEMS_PER_PAGE;
        return $dataProvider;
    }

    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('comment', 'required', 'on'=>'ticket'),
            array('sum', 'numerical', 'integerOnly' => false, 'min' => 0),
        ));
    }

}