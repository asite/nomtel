<?php

class POLoginForm extends CFormModel
{
    public $number;
    public $password;

    public function rules() {
        return array(
            array('number,password','safe')
        );
    }

    public function attributeLabels() {
        return array(
            'number'=>'Номер телефона',
            'password'=>'Пароль'
        );
    }
}
