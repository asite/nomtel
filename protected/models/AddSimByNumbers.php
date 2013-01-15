<?php

class AddSimByNumbers extends CFormModel
{
    public $numbers;
    public $operator;
    public $tariff;
    public $where;
    public $company;
    public $region;

    public function rules()
    {
        return array(
            array('numbers, operator, tariff, company, where, region', 'required'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'numbers' => Number::label(2),
            'operator' => Operator::label(1),
            'region' => OperatorRegion::label(1),
            'tariff' => Tariff::label(1),
            'where' => 'Куда передать карты?',
            'company' => Company::label(1)
        );
    }
}