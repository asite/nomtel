<?php

class LoadBalanceReport extends CFormModel
{
    public $file;
    public $operator;
    public $comment;

    public function rules() {
        return array(
            array('operator,comment','required'),
            array('file','file','types'=>'xls,xlsx')
        );
    }

    public function attributeLabels() {
        return array(
            'file'=>Yii::t('app','File'),
            'operator'=>Operator::model()->label(1),
            'comment'=>Yii::t('app','Comment'),
        );
    }
}
