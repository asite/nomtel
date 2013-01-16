<?php

Yii::import('application.models._base.BaseBalanceReportNumber');

class BalanceReportNumber extends BaseBalanceReportNumber
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function rules()
    {
        $rules=parent::rules();
        $this->addRules($rules,array(
            array('balance', 'numerical', 'integerOnly' => false),
        ));
        $this->delRules($rules,array(
            array('balance', 'length'),
        ));

        return $rules;
    }

}