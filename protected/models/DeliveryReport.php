<?php

Yii::import('application.models._base.BaseDeliveryReport');

class DeliveryReport extends BaseDeliveryReport
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function __toString() {
        return '№'.$this->id.' '.Yii::t('app','from').' '.$this->dt;
    }

    public function search() {
        $criteria = new CDbCriteria;

        $criteria->alias='delivery_report';
        $criteria->compare('delivery_report.id', $this->id);
        $criteria->compare('delivery_report.agent_id', $this->agent_id);
        $criteria->compare('delivery_report.bonus_report_id', $this->bonus_report_id);
        $criteria->compare('delivery_report.dt', $this->dt, true);
        $criteria->compare('delivery_report.sum', $this->sum);

        $dataProvider=new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));

        $dataProvider->pagination->pageSize=self::ITEMS_PER_PAGE;
        return $dataProvider;
    }

}