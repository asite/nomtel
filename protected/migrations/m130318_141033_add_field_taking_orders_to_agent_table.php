<?php

class m130318_141033_add_field_taking_orders_to_agent_table extends CDbMigration
{
	public function safeUp()
	{
		$this->execute("
            ALTER TABLE `agent`
			ADD `taking_orders` tinyint(1) NOT NULL DEFAULT '0',
			COMMENT='Хранит данные агентов. База (админ) имеет запись с id 1';
        ");
	}

	public function safeDown()
	{
		echo "m130318_141033_add_field_taking_orders_to_agent_table does not support migration down.\n";
		return false;
	}
}