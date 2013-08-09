<?php

Yii::import('application.models._base.BaseUser');

class User extends BaseUser
{
    const TAG = __CLASS__;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function representingColumn()
    {
        return 'username';
    }

    public function behaviors()
    {
        return array(
            'loggable' => array(
                'class' => 'ModelLoggableBehavior',
                'authComponentName' => 'user'
            ),
        );
    }

    static public function getStatusTitle($id)
    {
        $list = self::getStatusList();
        return $list[$id];
    }

    static public function getStatusList()
    {
        static $list;

        if (!isset($list))
            $list = array(
                ModelLoggableBehavior::STATUS_ACTIVE => Yii::t('app', 'Active'),
                ModelLoggableBehavior::STATUS_BLOCKED => Yii::t('app', 'Blocked')
            );

        return $list;
    }

    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('username', 'unique', 'message' => Yii::t('app', 'User with this username already exists'), 'skipOnError' => true),
            array('password', 'required', 'on' => 'create')
        ));
    }

    public function sendSmsWithLoginData($number) {
        $msg='Ваш логин: '.$this->username.' пароль '.$this->password.' . Вход в систему по адресу '.Yii::app()->request->hostInfo;
        Sms::send($number,$msg);
    }

    public function search()
    {
        $data_provider = parent::search();
        $data_provider->pagination->pageSize = self::ITEMS_PER_PAGE;
        $data_provider->setSort(array(
            'defaultOrder' => 'username asc'
        ));
        return $data_provider;
    }

}