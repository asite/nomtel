<?php

class CashierRestoreNumberSearch extends CFormModel {
    public $number;

    public function rules() {
        return array(
            array('number','safe')
        );
    }
}