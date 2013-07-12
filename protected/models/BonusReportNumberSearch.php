<?php


class BonusReportNumberSearch extends CFormModel
{
    public $number;
    public $tariff_id;
    public $turnover;
    public $rate;
    public $sum;
    public $agent_id;
    public $status;

    public function rules() {
        return array(
            array('number,tariff_id,turnover,rate,sum,agent_id,status','safe')
        );
    }
}
