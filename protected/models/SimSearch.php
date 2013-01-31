<?php


class SimSearch extends CFormModel
{
    public $agent_id;
    public $number;
    public $icc;
    public $operator_id;
    public $tariff_id;
    public $status;
    public $balance_status;

    public function rules() {
        return array(
            array('agent_id,number,icc,operator_id,tariff_id,status,balance_status','safe')
        );
    }
}
