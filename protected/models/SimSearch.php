<?php


class SimSearch extends CFormModel
{
    public $agent_id;
    public $number;
    public $icc;
    public $operator_id;
    public $tariff_id;
    public $operator_region_id;
    public $status;
    public $support_status;
    public $support_operator_id;
    public $balance_status;

    public function rules() {
        return array(
            array('agent_id,number,icc,operator_id,tariff_id,operator_region_id,status,balance_status,support_status,support_operator_id','safe')
        );
    }
}
