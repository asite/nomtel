<?php

class AddSim extends CFormModel
{
    public $ICCFirst;
    public $ICCBegin;
    public $ICCEnd;
    public $operator;
    public $tariff;
    public $where;
    public $phone;

    public $ICCPersonalAccount;
    public $ICCBeginFew;
    public $ICCEndFew;
    public $region;
    public $company;


    public function rules()
    {
        return array(
            array('ICCFirst, ICCBegin, ICCEnd, phone, operator, tariff', 'required'),
            array('ICCFirst, ICCBegin, ICCEnd, ICCPersonalAccount, ICCBeginFew, ICCEndFew', 'numerical'),
            array('ICCFirst', 'length'),
            array('ICCBegin, ICCEnd', 'length'),
            array('ICCBeginFew', 'length', 'max' => 20),
            array('ICCEndFew', 'length', 'max' => 3),
            array('region,company','safe')
        );
    }

    public function attributeLabels()
    {
        return array(
            'ICCFirst' => 'ICC Первые цифры',
            'ICCBegin' => 'ICC Начало',
            'ICCBeginFew' => 'ICC Начало',
            'ICCEnd' => 'ICC Конец',
            'ICCEndFew' => 'ICC Конец',
            'phone' => 'Телефон',
            'ICCPersonalAccount' => 'Личный счёт ',
            'operator' => 'Выбор оператора',
            'tariff' => 'Тарифный план',
            'where' => 'Куда передать карты?',
            'region' => OperatorRegion::label(1),
            'company' => Company::label(1),
        );
    }
}