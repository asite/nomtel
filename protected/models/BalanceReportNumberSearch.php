<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pavel
 * Date: 04.01.13
 * Time: 15:54
 * To change this template use File | Settings | File Templates.
 */
class BalanceReportNumberSearch extends CFormModel
{
    public $personal_account;
    public $number;
    public $balance;

    public function rules() {
        return array(
            array('personal_account,number,balance','safe')
        );
    }
}
