<?php

class CashierSellNumberSearch extends CFormModel {
    public $type;
    public $number;

    public function rules() {
        return array(
            array('type,number','safe')
        );
    }
}