<?php

class BalanceReportNumberSearch extends CFormModel
{
    public $personal_account;
    public $number;
    public $balance;

    public function rules()
    {
        return array(
            array('personal_account,number,balance', 'safe')
        );
    }
}
