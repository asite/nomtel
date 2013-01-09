<?php

class m130109_083924_number_personal_account_can_be_null extends CDbMigration
{
	public function safeUp()
	{
        $this->execute("
            ALTER TABLE `number`
            CHANGE `personal_account` `personal_account` varchar(50) COLLATE 'utf8_general_ci' NULL AFTER `number`,
            COMMENT='';
        ");
	}

	public function safeDown()
	{
		echo "m130109_083924_number_personal_account_can_be_null does not support migration down.\n";
		return false;
	}
}