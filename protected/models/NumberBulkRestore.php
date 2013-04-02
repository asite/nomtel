<?php
class NumberBulkRestore extends CFormModel
{
    public $data;

    public function rules() {
        return array(
            array('data','required'),
        );
    }

    public function attributeLabels() {
        return array(
            'data'=>'Данные'
        );
    }
}
