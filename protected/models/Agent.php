<?php

Yii::import('application.models._base.BaseAgent');

class Agent extends BaseAgent
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function __toString()
    {
        return $this->surname . ' ' . $this->name . ' ' . $this->middle_name;
    }

    public function search()
    {
        $data_provider = parent::search();

        $data_provider->setSort(array(
            'defaultOrder' => 'surname,name,middle_name'
        ));
        return $data_provider;
    }

    public static function getComboList($data = array())
    {
        $agents = Yii::app()->db->createCommand("select id,name,surname,middle_name from " .
            self::model()->tableName() . " where parent_id=:parent_id" .
            " order by surname,name,middle_name")->queryAll(true, array(':parent_id' => loggedAgentId()));

        foreach ($agents as $v) {
            $data[$v['id']] = $v['surname'] . ' ' . $v['name'] . ' ' . $v['middle_name'];
        }

        return $data;
    }

    public static function getFullComboList($withoutMe = true,$data=array())
    {
        if ($withoutMe) $id = loggedAgentId(); else $id = '';
        $agents = Yii::app()->db->createCommand("select id,name,surname,middle_name from " .
            self::model()->tableName() . " where id!=:id" .
            " order by surname,name,middle_name")->queryAll(true, array(':id' => $id));

        foreach ($agents as $v) {
            $data[$v['id']] = $v['surname'] . ' ' . $v['name'] . ' ' . $v['middle_name'];
        }

        return $data;
    }

    public function getBalance() {
        return $this->getAllPaymentsSum()-$this->getAllActsSum();
    }

    public function getAllPaymentsSum() {
        return floatval(Yii::app()->db->createCommand('select sum(sum) from ' . Payment::model()->tableName() .
            ' where agent_id=:agent_id')->queryScalar(array('agent_id' => $this->id)));
    }

    public function getAllActsSum() {
        return floatval(Yii::app()->db->createCommand('select sum(sum) from ' . Act::model()->tableName() .
            ' where agent_id=:agent_id')->queryScalar(array('agent_id' => $this->id)));
    }

    public function getSimCount() {
        $count=intval($this->dbConnection->createCommand(
            "select count(*) from ".Sim::model()->tableName()." where is_active=1 and parent_agent_id=:agent_id")->
            queryScalar(array(':agent_id'=>$this->id)));

        return $count;
    }

    public function getActiveSimCount() {
        $count=intval($this->dbConnection->createCommand(
            "select count(*) from sim s
             join number n on (n.sim_id=s.parent_id)
             where s.is_active=1 and s.parent_agent_id=:agent_id and
             (
              (s.operator_id=1 and n.balance_status in ('ACTIVE'))
              or
              (s.operator_id=2 and n.balance_status in ('POSITIVE_DYNAMIC','NEGATIVE_DYNAMIC','BALANCE_STATUS_NORMAL'))
             )
            ")->queryScalar(array(':agent_id'=>$this->id)));

        return $count;
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
            'paymentsSum' => Yii::t('app', 'Payments Sum'),
            'actsSum' => Yii::t('app', 'Delivery Reports Sum'),
        ));
    }
}