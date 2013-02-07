<?php
class NumberSupportNumber extends CFormModel
{
    public $number;

    public function rules() {
        return array(
            array('number','required'),
            array('number','exist','className'=>'Number','attributeName'=>'number','message'=>'Номер {value} отсутствует в базе'),
        );
    }

    public function attributeLabels() {
        return array(
            'number'=>Yii::t('app','Number'),
        );
    }
}
