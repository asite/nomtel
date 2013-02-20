<?php

Yii::import('application.models._base.BaseBonusReport');

class BonusReport extends BaseBonusReport
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function __toString()
    {
        return $this->comment;
    }
}