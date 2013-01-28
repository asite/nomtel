<?php


class SimSearch extends CFormModel
{
    public $agent_id;
    public $number;
    public $icc;
    public $operator_id;
    public $tariff_id;
    public $status;
    public $balance_status;

    public function rules() {
        return array(
            array('agent_id,number,icc,operator_id,tariff_id,status,balance_status','safe')
        );
    }

    public function attributeLabels() {
        return array(
            'agent_id'=>Agent::label(1),
            'number'=>Yii::t('app','Number'),
            'icc'=>Yii::t('app','Icc'),
            'operator_id'=>Operator::label(1),
            'tariff_id'=>Tariff::label(1),
            'status'=>Yii::t('app','Status'),
            'balance_status'=>Yii::t('app','Balance Status'),
        );
    }
}
