<?php

class m130102_105736_create_agent_stats_fields extends CDbMigration
{
	public function safeUp()
	{
        $this->execute("
        ALTER TABLE `agent`
        ADD `stat_acts_sum` double NOT NULL DEFAULT '0',
        ADD `stat_payments_sum` double NOT NULL DEFAULT '0' AFTER `stat_acts_sum`,
        ADD `stat_sim_count` int NOT NULL DEFAULT '0' AFTER `stat_payments_sum`,
        COMMENT=''
        ");

        $agents=Agent::model()->findAll();
        foreach($agents as $agent) {
            $agent->recalcAllStats();
            $agent->save();
        }
	}

	public function safeDown()
	{
		echo "m130102_105736_create_agent_stats_fields does not support migration down.\n";
		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}