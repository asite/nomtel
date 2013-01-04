<?php

Yii::import('application.models._base.BaseAgentReferralRate');

class AgentReferralRate extends BaseAgentReferralRate
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('rate', 'numerical', 'min' => 0, 'max' => 100)
        ));
    }
}