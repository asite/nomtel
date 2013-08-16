<?php

class CashierRestoreForm extends CFormModel
{
    public $icc;

    public function rules() {
        return array(
            array('icc','required'),
        );
    }

    public function attributeLabels() {
        return array(
            'icc'=>'Icc',
        );
    }
}
