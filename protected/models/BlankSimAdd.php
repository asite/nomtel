<?php
class BlankSimAdd extends CFormModel
{
    public $type;
    public $operator_id;
    public $icc;

    public function rules() {
        return array(
            array('type,operator_id,icc','required')
        );
    }

    public function attributeLabels() {
        return array(
            'type'=>Yii::t('app','Type'),
            'operator_id'=>Operator::label(),
            'icc'=>Yii::t('app','Icc')
        );
    }
}
