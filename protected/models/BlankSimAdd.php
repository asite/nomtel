<?php
class BlankSimAdd extends CFormModel
{
    public $type;
    public $operator_id;
    public $operator_region_id;
    public $icc;

    public function rules() {
        return array(
            array('type,operator_id,operator_region_id,icc','required')
        );
    }

    public function attributeLabels() {
        return array(
            'type'=>Yii::t('app','Type'),
            'operator_id'=>Operator::label(),
            'operator_region_id'=>OperatorRegion::label(),
            'icc'=>Yii::t('app','Icc')
        );
    }
}
