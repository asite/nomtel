<?php

Yii::import('application.models._base.BaseTicket');

class Ticket extends BaseTicket
{
    const STATUS_NEW = 'NEW';
    const STATUS_VIEWED = 'VIEWED';
    const STATUS_CLOSED = 'CLOSED';

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function getStatusLabel($type) {
      $statuses = array(
        self::STATUS_NEW => Yii::t('app','NEW'),
        self::STATUS_VIEWED => Yii::t('app','VIEWED'),
        self::STATUS_CLOSED => Yii::t('app','CLOSED'),
      );
      return $statuses[$type];
    }
}