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
        $rules=parent::rules();
        $this->addRules($rules,array(
            array('sum', 'numerical', 'integerOnly' => false, 'min' => 0),
            array('comment', 'required', 'on'=>'ticket'),
        ));
        $this->delRules($rules,array(
            array('sum', 'length'),
        ));

        return $rules;
    }

    public function updateSimCount() {
        $this->sim_count=Sim::model()->countByAttributes(array('act_id'=>$this->id));
    }
}