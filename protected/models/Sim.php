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
    if (empty($ids) || !is_array($ids)) return 0;
    $criteria=new CDbCriteria;
      $criteria->select='sum(number_price) as number_price';
    if (is_array($ids)) $criteria->addInCondition('id', $ids);
    return $this->find($criteria)->getAttribute('number_price');
  }

    public function search() {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('act_id', $this->act_id);
        $criteria->compare('personal_account', $this->personal_account, true);

        if ($this->number!=Yii::t('app','WITHOUT NUMBER'))
            $criteria->compare('number', $this->number, true);
        else
            $criteria->addCondition("(number='' or number is null)");

        if ($this->agent_id!=='0')
            $criteria->compare('agent_id', $this->agent_id);
        else
            $criteria->addCondition("agent_id is null");

        $criteria->compare('number_price', $this->number_price);
        $criteria->compare('icc', $this->icc, true);
        $criteria->compare('operator_id', $this->operator_id);
        $criteria->compare('tariff_id', $this->tariff_id);

        $dataProvider=new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));

        $dataProvider->pagination->pageSize=self::ITEMS_PER_PAGE;
        return $dataProvider;
    }

    public function __toString() {
        return $this->shortIcc;
    }
}