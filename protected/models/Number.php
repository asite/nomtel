<?php

Yii::import('application.models._base.BaseNumber');

class Number extends BaseNumber
{
    const STATUS_UNKNOWN = 'UNKNOWN';
    const STATUS_FREE = 'FREE';
    const STATUS_CONNECTED = 'CONNECTED';

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}