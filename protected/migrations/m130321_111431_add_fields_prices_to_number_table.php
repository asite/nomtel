<?php

class m130321_111431_add_fields_prices_to_number_table extends CDbMigration
{
	public function safeUp()
	{
		$this->execute("
			ALTER TABLE `number`
			ADD `sim_price` decimal(14,2) NULL DEFAULT '0' AFTER `service_password`,
			ADD `number_price` decimal(14,2) NULL DEFAULT '0' AFTER `sim_price`,
			COMMENT='Хранит данные по номеру';"
		);
		$this->execute("
			ALTER TABLE `number`
			CHANGE `sim_price` `sim_price` decimal(14,2) NOT NULL DEFAULT '0.00' AFTER `service_password`,
			CHANGE `number_price` `number_price` decimal(14,2) NOT NULL DEFAULT '0.00' AFTER `sim_price`,
			COMMENT='Хранит данные по номеру';
		");
	}

	public function safeDown()
	{
		echo "m130321_111431_add_fields_prices_to_number_table does not support migration down.\n";
		return false;
	}
}