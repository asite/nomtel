<?php

Yii::import('application.models._base.BaseCompany');

class Company extends BaseCompany
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}