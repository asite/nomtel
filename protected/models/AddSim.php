<?php

class AddSim extends CFormModel {
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


  public function rules() {
    return array(
      array('ICCFirst, ICCBegin, ICCEnd, ICCPersonalAccount, phone, operator, tariff', 'required'),
      array('ICCFirst, ICCBegin, ICCEnd, ICCPersonalAccount, ICCBeginFew, ICCEndFew', 'numerical'),
      array('ICCFirst', 'length'),
      array('ICCBegin, ICCEnd', 'length'),
      array('ICCBeginFew', 'length', 'max'=>15),
      array('ICCEndFew', 'length', 'max'=>3),
    );
  }

  public function attributeLabels() {
    return array(
      'ICCFirst'=>'ICC Первые цифры',
      'ICCBegin'=>'ICC Начало',
      'ICCBeginFew'=>'ICC Начало',
      'ICCEnd'=>'ICC Конец',
      'ICCEndFew'=>'ICC Конец',
      'phone'=>'Телефон',
      'ICCPersonalAccount'=>'Личный счёт ',
      'operator'=>'Выбор оператора',
      'tariff'=>'Тарифный план',
      'where'=>'Куда передать карты?',
    );
  }
}