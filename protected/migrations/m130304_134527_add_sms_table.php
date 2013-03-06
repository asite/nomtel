<?php

class m130304_134527_add_sms_table extends CDbMigration
{
	public function safeUp()
	{
		$this->execute("CREATE TABLE `sms_log` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
					  `user_id` bigint(20) NOT NULL,
					  `number` varchar(50) NOT NULL,
					  `text` varchar(140) NOT NULL,
					  `dt` timestamp NOT NULL
					) COMMENT='' COLLATE 'utf8_general_ci';");
	}

	public function safeDown()
	{
		echo "m130304_134527_add_sms_table does not support migration down.\n";
		return false;
	}
}