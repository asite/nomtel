<?php


class CashierNumberSearch extends CFormModel
{
    public $number;
    public $tariff_id;
    public $operator_region_id;

    public function rules() {
        return array(
            array('number,tariff_id,operator_region_id','safe')
        );
    }
}
