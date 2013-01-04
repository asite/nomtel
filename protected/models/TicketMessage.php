<?php

Yii::import('application.models._base.BaseTicketMessage');

class TicketMessage extends BaseTicketMessage
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}