<?php


class BlankSimSearch extends CFormModel
{
    public $type;
    public $icc;
    public $operator_id;
    public $operator_region_id;
    public $used_dt;
    public $used_support_operator_id;
    public $number;

    public function rules() {
        return array(
            array('type,icc,operator_id,operator_region_id,used_dt,used_support_operator_id,number','safe')
        );
    }
}
