<?php

class MegafonAppRestoreNumberProcess extends CFormModel {
    public $number;
    public $icc;

    public function rules() {
        return array(
            array('number,icc','required'),
        );
    }

    public function attributeLabels() {
        return array(
            'number'=>Yii::t('app','Number'),
            'icc'=>Yii::t('app','Icc')
        );
    }

}