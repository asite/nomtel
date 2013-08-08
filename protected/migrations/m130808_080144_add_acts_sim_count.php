<?php

class m130808_080144_add_acts_sim_count extends CDbMigration
{
	public function up()
	{
        /*
        $this->execute("
            ALTER TABLE `act`
            ADD `sim_count` int NULL COMMENT 'кол-во переданных SIM' AFTER `sum`,
            COMMENT='Хранит акты агентов. Под актом понимается как передача симок агенту, так и просто списание средств за что-либо. Т.е. акт это что-то, приводящее к списанию средств со счета агента';
        ");
        */

        $trx=Yii::app()->db->beginTransaction();

        $acts=Act::model()->findAllByAttributes(array('type'=>Act::TYPE_SIM));
        foreach($acts as $act) {
            $act->sim_count=Sim::model()->countByAttributes(array('act_id'=>$act->id));
            $act->save();
        }

        $trx->commit();
	}

	public function down()
	{
		echo "m130808_080144_add_acts_sim_count does not support migration down.\n";
		return false;
	}
}