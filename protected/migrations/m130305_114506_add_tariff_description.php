<?php

class m130305_114506_add_tariff_description extends CDbMigration
{
	public function safeUp()
	{
		$this->execute("ALTER TABLE `tariff` ADD `description` text COLLATE 'utf8_general_ci' NULL AFTER `title`, COMMENT='';");
	}

	public function safeDown()
	{
		echo "m130305_114506_add_tariff_description does not support migration down.\n";
		return false;
	}
}