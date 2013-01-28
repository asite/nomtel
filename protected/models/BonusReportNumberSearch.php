<?php


class BonusReportNumberSearch extends CFormModel
{
    public $number;
    public $personal_account;
    public $turnover;
    public $rate;
    public $sum;
    public $agent_id;
    public $status;

    public function rules() {
        return array(
            array('number,personal_account,turnover,rate,sum,agent_id,status','safe')
        );
    }
}
