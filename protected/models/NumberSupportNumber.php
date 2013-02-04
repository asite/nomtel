<?php
class NumberSupportNumber extends CFormModel
{
    public $number;

    public function rules() {
        return array(
            array('number','required'),
            array('number','exist','className'=>'Number','criteria'=>array('condition'=>'status=:free_status','params'=>array(':free_status'=>Number::STATUS_FREE)),'attributeName'=>'number','message'=>'Номер {value} не свободен'),
        );
    }

    public function attributeLabels() {
        return array(
            'number'=>Yii::t('app','Number'),
        );
    }
}
