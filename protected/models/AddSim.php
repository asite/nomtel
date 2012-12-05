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
      array('ICCFirst, ICCBegin, ICCEnd, ICCPersonalAccount, ICCBeginFew, ICCEndFew, phone', 'required'),
      array('ICCFirst, ICCBegin, ICCEnd, ICCPersonalAccount, ICCBeginFew, ICCEndFew', 'numerical'),
      array('ICCFirst', 'length', 'max'=>8),
      array('ICCBegin, ICCEnd', 'length', 'max'=>4),
      array('ICCBeginFew', 'length', 'max'=>15),
      array('ICCEndFew', 'length', 'max'=>3),
    );
  }

  public function attributeLabels() {
    return array(
      'ICCFirst'=>'ICC Первые цифры',
      'ICCBegin'=>'ICC Начало',
      'ICCEnd'=>'ICC Конец',
      'operator'=>'Выбор оператора',
      'tariff'=>'Тарифный план',
      'where'=>'Куда передать карты?',
    );
  }
}