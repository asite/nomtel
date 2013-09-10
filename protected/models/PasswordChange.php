<?php

class PasswordChange extends CFormModel {
    public $password;
    public $password2;

    public function rules() {
        return array(
            array('password','required'),
            array('password2','compare','compareAttribute'=>'password')
        );
    }

    public function attributeLabels() {
        return array(
            'password'=>'Ваш новый пароль',
            'password2'=>'Повторите его'
        );
    }
}