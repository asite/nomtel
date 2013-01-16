<?php

Yii::import('application.models._base.BasePayment');

class Payment extends BasePayment
{
    const TYPE_NORMAL = 'NORMAL';
    const TYPE_BONUS = 'BONUS';

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function rules()
    {
        $rules=parent::rules();
        $this->addRules($rules,array(
            array('sum', 'numerical', 'integerOnly' => false, 'min' => 0),
        ));
        $this->delRules($rules,array(
            array('sum', 'length'),
        ));

        return $rules;
    }

}