<?php

class m130117_215402_add_file_dt_field extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `file`
            ADD `dt` timestamp NOT NULL AFTER `id`,
            CHANGE `url` `url` varchar(200) COLLATE 'utf8_general_ci' NULL AFTER `dt`,
            COMMENT='';
        ");
    }

	public function down()
	{
		echo "m130117_215402_add_file_dt_field does not support migration down.\n";
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