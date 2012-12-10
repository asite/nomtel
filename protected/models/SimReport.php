<?php

class SimReport extends CFormModel {
    public $message;

    public function rules() {
        return array(
            array('message','required')
        );
    }

    public function attributeLabels() {
        return array(
            'message'=>Yii::t('app','Problem description')
        );
    }

}
