<?php

class POLoginForm extends CFormModel
{
    public $number;
    public $password;

    public function rules() {
        return array(
            array('number,password','safe'),
            array('number','required','on'=>'restore'),
            array('number','validateNumber','skipOnError'=>true,'on'=>'restore')
        );
    }

    public function attributeLabels() {
        return array(
            'number'=>'Номер телефона',
            'password'=>'Пароль'
        );
    }

    public function validateNumber() {
        if (Number::model()->countByAttributes(array('number'=>Number::getNumberFromFormatted($this->number)))!=1) {
            $this->addError('number','Данный номер не существует');
        }
    }
}
