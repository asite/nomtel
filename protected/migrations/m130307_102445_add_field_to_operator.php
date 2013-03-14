<?php

class m130307_102445_add_field_to_operator extends CDbMigration
{
    public function safeUp()
	{
		$this->execute("ALTER TABLE `operator` ADD `html_commands` text COLLATE 'utf8_general_ci' NULL AFTER `title`, COMMENT='';");
		$this->execute("ALTER TABLE `operator` ADD `html_news` text COLLATE 'utf8_general_ci' NULL AFTER `title`, COMMENT='';");
		$this->execute("ALTER TABLE `operator` ADD `html_internet` text COLLATE 'utf8_general_ci' NULL AFTER `title`, COMMENT='';");
	}

	public function safeDown()
	{
		echo "m130307_102445_add_field_to_operator does not support migration down.\n";
		return false;
	}
}