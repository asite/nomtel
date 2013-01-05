<?php

class m130105_090848_add_city_column_to_agent_table extends CDbMigration
{
	public function safeUp()
	{
		$this->execute("ALTER TABLE `agent` ADD `city` varchar(100) COLLATE 'utf8_general_ci' NULL AFTER `phone_3`, COMMENT=''");
	}

	public function safeDown()
	{
		echo "m130105_090848_add_city_column_to_agent_table does not support migration down.\n";
		return false;
	}
}