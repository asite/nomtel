<?php

class CashierNumberRestoreFinishForm extends CFormModel {
    public $sum;

    public function rules() {
        return array(
            array('sum','required'),
            array('sum','numerical')
        );
    }

    public function attributeLabels() {
        return array(
            'sum' => Yii::t('app', 'Sum'),
        );
    }
}