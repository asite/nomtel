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

    public static function getFullComboList($withoutMe = true)
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

    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('parent_id', 'unsafe')
        ));
    }

    public function recalcBalance()
    {
        $this->recalcStatPaymentsSum();
        $this->recalcStatActsSum();
        $this->balance = $this->stat_payments_sum - $this->stat_acts_sum;
    }

    public function recalcStatPaymentsSum()
    {
        $this->stat_payments_sum = floatval(Yii::app()->db->createCommand('select sum(sum) from ' . Payment::model()->tableName() .
            ' where agent_id=:agent_id')->queryScalar(array('agent_id' => $this->id)));
    }

    public function recalcStatActsSum()
    {
        $this->stat_acts_sum = floatval(Yii::app()->db->createCommand('select sum(sum) from ' . Act::model()->tableName() .
            ' where agent_id=:agent_id')->queryScalar(array('agent_id' => $this->id)));
    }

    public function recalcStatSimCount()
    {
        $this->stat_sim_count = Sim::model()->countByAttributes(array('parent_agent_id' => $this->id));
    }

    public function recalcAllStats()
    {
        $this->recalcBalance();
        $this->recalcStatSimCount();
    }

    /**
     * Procedure updates balance and acts/payments statistic
     * @param $agent_id
     * @param $delta_balance
     * @param bool $reverse = true, if doing deleting existing act/payment
     */
    public static function deltaBalance($agent_id, $delta_balance, $reverse = false)
    {
        static $cmdBalance;

        if (!$cmdBalance) $cmdBalance = Yii::app()->db->createCommand("update agent set
            balance=balance+:delta_balance,
            stat_acts_sum=stat_acts_sum+:delta_stat_acts_sum,
            stat_payments_sum=stat_payments_sum+:delta_stat_payments_sum
        where id=:agent_id");

        $delta_stat_payments_sum = 0;
        $delta_stat_acts_sum = 0;
        if ($reverse) {
            if ($delta_balance < 0) {
                $delta_stat_payments_sum = $delta_balance;
            } else {
                $delta_stat_acts_sum = -$delta_balance;
            }
        } else {
            if ($delta_balance > 0) {
                $delta_stat_payments_sum = $delta_balance;
            } else {
                $delta_stat_acts_sum = -$delta_balance;
            }
        }

        $cmdBalance->execute(array(
            ':agent_id' => $agent_id,
            ':delta_balance' => $delta_balance,
            ':delta_stat_acts_sum' => $delta_stat_acts_sum,
            ':delta_stat_payments_sum' => $delta_stat_payments_sum
        ));
    }

    public static function deltaSimCount($agent_id, $delta_sim_count)
    {
        Agent::model()->updateCounters(array('stat_sim_count' => $delta_sim_count), 'id=:id', array('id' => $agent_id));
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
            'paymentsSum' => Yii::t('app', 'Payments Sum'),
            'actsSum' => Yii::t('app', 'Delivery Reports Sum'),
        ));
    }
}