<?php

class NumberInfoEdit extends CFormModel
{
    public $number;
    public $tariff;
    public $agent;
    public $operator;
    public $operator_region;
    public $icc;
    public $status;
    public $company;
    public $dt;
    public $director;
    public $codeword;
    public $bad;
    public $personal_account;
    public $sim_price;
    public $number_price;
    public $turnover_sim;

    public function rules()
    {
        return array(
            array('number, tariff, agent', 'safe')
        );
    }

    public function getAttribute($name)
    {
        if(property_exists($this,$name))
            return $this->$name;
        elseif(isset($this->_attributes[$name]))
            return $this->_attributes[$name];
    }

    public function hasAttribute($name)
    {
        return property_exists($this,$name) || isset($this->getMetaData()->columns[$name]);
    }

    public function attributeLabels()
    {
        return array(
            'number' => Yii::t('app','Number'),
            'tariff' => Yii::t('app','Tariff'),
            'agent' => Yii::t('app','the Agent'),
            'operator' => Yii::t('app','Operator'),
            'operator_region' => Yii::t('app','OperatorRegion'),
            'icc' => Yii::t('app','icc'),
            'status' => Yii::t('app','Status'),
            'company' => Yii::t('app','Company'),
            'dt' => Yii::t('app','Date connection'),
            'director' => Yii::t('app','Director'),
            'codeword' => Yii::t('app','Codeword'),
            'bad' => Yii::t('app','BAD'),
            'personal_account' => Yii::t('app','personal_account'),
            'sim_price' => Yii::t('app','sim_price'),
            'number_price' => Yii::t('app','number_price'),
            'turnover_sim' => Yii::t('app','turnover_sim')
        );
    }
}