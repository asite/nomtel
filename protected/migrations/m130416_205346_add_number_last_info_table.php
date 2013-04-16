<?php

class m130416_205346_add_number_last_info_table extends CDbMigration
{
	public function up()
	{
        $this->execute("
            CREATE TABLE `number_last_info` (
              `number_id` bigint(20) NOT NULL COMMENT 'номер',
              `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'дата письма',
              `text` mediumtext NOT NULL COMMENT 'текст письма',
              PRIMARY KEY (`number_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Хранит последний email с информацией по номеру';
        ");
	}

	public function down()
	{
		echo "m130416_205346_add_number_last_info_table does not support migration down.\n";
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