<?php


class CashierStatistic extends CFormModel
{
    public $date_from;
    public $date_to;

    public function rules() {
        return array(
            array('date_from,date_to','safe'),
        );
    }

    public function attributeLabels() {
        return array(
            'date_from'=>'Дата',
            'date_to'=>'По',
        );
    }
}
