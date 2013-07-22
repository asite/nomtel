<?php


class SupportBeelineNumberSearch extends CFormModel
{
    public $personal_account;
    public $icc;
    public $number;
    public $name;
    public $surname;
    public $middle_name;
    public $registration_address;

    public function rules() {
        return array(
            array('personal_account,icc,number,name,surname,middle_name,registration_address','safe')
        );
    }
}
