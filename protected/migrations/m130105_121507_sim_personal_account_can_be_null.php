<?php

class m130105_121507_sim_personal_account_can_be_null extends CDbMigration
{
	public function safeUp()
	{
		$this->execute("ALTER TABLE `sim` CHANGE `personal_account` `personal_account` varchar(50) COLLATE 'utf8_general_ci' NULL AFTER `id`,COMMENT=''");
	}

	public function safeDown()
	{
		echo "m130105_121507_sim_personal_account_can_be_null does not support migration down.\n";
		return false;
	}
}