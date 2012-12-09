<?php

Yii::import('application.models._base.BaseSim');

class Sim extends BaseSim
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function getShortIcc() {
        return '...'.substr($this->icc,-6);
    }

  public function getTotalNumberPrice($ids='') {
    $criteria=new CDbCriteria;
      $criteria->select='sum(number_price) as number_price';
    if (is_array($ids)) $criteria->addInCondition('id', $ids);
    return $this->find($criteria)->getAttribute('number_price');
  }
}