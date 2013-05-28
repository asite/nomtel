<?php

class m130522_104558_delete_agent_stat_fields extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `agent`
            DROP `balance`,
            DROP `stat_acts_sum`,
            DROP `stat_payments_sum`,
            DROP `stat_sim_count`,
            COMMENT='Хранит данные агентов. База (админ) имеет запись с id 1';
        ");
	}

	public function down()
	{
		echo "m130522_104558_delete_agent_stat_fields does not support migration down.\n";
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