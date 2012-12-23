<?php

Yii::import('application.models._base.BaseAgent');

class Agent extends BaseAgent
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function __toString() {
        return $this->surname.' '.$this->name.' '.$this->middle_name;
    }

    public function search() {
        $data_provider = parent::search();

        $data_provider->setSort(array(
            'defaultOrder' => 'surname,name,middle_name'
        ));
        return $data_provider;
    }

    public static function getComboList($data=array()) {
        $agents=Yii::app()->db->createCommand("select id,name,surname,middle_name from ".
            self::model()->tableName()." where parent_id=:parent_id".
            " order by surname,name,middle_name")->queryAll(true,array(':parent_id'=>loggedAgentId()));

        foreach($agents as $v) {
            $data[$v['id']]=$v['surname'].' '.$v['name'].' '.$v['middle_name'];
        }

        return $data;
    }

    public static function getFullComboList($withoutMe=true) {
        if ($withoutMe) $id = loggedAgentId(); else $id = '';
        $agents=Yii::app()->db->createCommand("select id,name,surname,middle_name from ".
            self::model()->tableName()." where id!=:id".
            " order by surname,name,middle_name")->queryAll(true,array(':id'=>$id));

        foreach($agents as $v) {
            $data[$v['id']]=$v['surname'].' '.$v['name'].' '.$v['middle_name'];
        }

        return $data;
    }

    public function rules() {
        return array_merge(parent::rules(),array(
            array('parent_id','unsafe')
        ));
    }

    public function recalcBalance() {
        $this->balance=$this->getPaymentsSum()-$this->getDeliveryReportsSum();
    }

    public function getPaymentsSum() {
        return floatval(Yii::app()->db->createCommand('select sum(sum) from '.Payment::model()->tableName().
            ' where agent_id=:agent_id')->queryScalar(array('agent_id'=>$this->id)));
    }

    public function getDeliveryReportsSum() {
        return floatval(Yii::app()->db->createCommand('select sum(sum) from '.DeliveryReport::model()->tableName().
            ' where agent_id=:agent_id')->queryScalar(array('agent_id'=>$this->id)));
    }

    public function attributeLabels() {
        return array_merge(parent::attributeLabels(),array(
            'paymentsSum'=>Yii::t('app','Payments Sum'),
            'deliveryReportsSum'=>Yii::t('app','Delivery Reports Sum'),
        ));
    }
}