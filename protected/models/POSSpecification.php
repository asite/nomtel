<?php
class POSSpecification extends CFormModel
{
    public $dateRange;

    public function rules() {
        return array(
            array('dateRange','required')
        );
    }

    public function attributeLabels() {
        return array(
            'dateRange'=>Yii::t('app','Date Range')
        );
    }
}
