<?php
class NumberReport extends CFormModel
{
    public $number;
    public $abonent_number;
    public $message;

    public function rules() {
        return array(
            array('number,abonent_number,message','required'),
            array('number','exist','className'=>'Number','attributeName'=>'number','message'=>Yii::t('app',"Number {value} not found in database"))
        );
    }

    public function attributeLabels() {
        return array(
            'number'=>Yii::t('app','problem number'),
            'abonent_number'=>Yii::t('app','incoming number'),
            'message'=>Yii::t('app','Message')
        );
    }
}
