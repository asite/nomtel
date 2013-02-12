<?php

class PORestorePasswordForm extends CFormModel
{
    public $number;

    public function rules() {
        return array(
            array('number','required'),
            array('number','validateNumber','skipOnError'=>true)
        );
    }

    public function attributeLabels() {
        return array(
            'number'=>'Номер телефона',
        );
    }

    public function validateNumber() {
        if (Number::model()->countByAttributes(array('number'=>Number::getNumberFromFormatted($this->number)))!=1) {
            $this->addError('number','Данный номер не существует');
        }
    }
}
