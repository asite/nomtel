<?php

Yii::import('application.models._base.BaseBonusReportAgent');

class BonusReportAgent extends BaseBonusReportAgent
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function rules()
    {
        $rules=parent::rules();
        $this->addRules($rules,array(
            array('sum, sum_referrals', 'numerical', 'integerOnly' => false, 'min' => 0),
        ));
        $this->delRules($rules,array(
            array('sum, sum_referrals', 'length'),
        ));

        return $rules;
    }

}