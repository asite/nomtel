<?php


class CashierStatistic extends CFormModel
{
    public $date_from;
    public $date_to;
    public $support_operator_id;

    public function rules() {
        return array(
            array('date_from,date_to,support_operator_id','safe'),
        );
    }

    public function attributeLabels() {
        return array(
            'date_from'=>'Дата',
            'date_to'=>'По',
            'support_operator_id'=>'Кассир'
        );
    }
}
