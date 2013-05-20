<?php

class CashierNumberSearch2 extends CFormModel {
    public $support_operator_id;
    public $number;
    public $confirmed;

    public function rules() {
        return array(
            array('support_operator_id,number,confirmed','safe')
        );
    }
}