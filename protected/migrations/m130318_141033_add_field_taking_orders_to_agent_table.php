<?php

class m130318_141033_add_field_taking_orders_to_agent_table extends CDbMigration
{
	public function safeUp()
	{
		$this->execute("
            ALTER TABLE `tmp_number_region`
			ADD INDEX `start` (`start`),
			ADD INDEX `end` (`end`);
        ");
	}

	public function safeDown()
	{
		echo "m130318_141033_add_field_taking_orders_to_agent_table does not support migration down.\n";
		return false;
	}
}