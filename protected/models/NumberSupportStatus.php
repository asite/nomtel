<?php
class NumberSupportStatus extends CFormModel
{
    public $status;
    public $callback_dt;
    public $callback_name;

    public function rules() {
        return array(
            array('status','required'),
            array('callback_dt,callback_name','required','on'=>Number::SUPPORT_STATUS_CALLBACK),
            array('callback_dt','date','format'=>'dd.MM.yyyy hh:mm:ss'),
        );
    }

    public function attributeLabels() {
        return array(
            'status'=>Yii::t('app','Status'),
            'callback_dt'=>'Дата и время',
            'callback_name'=>Yii::t('app','Name')
        );
    }
}
