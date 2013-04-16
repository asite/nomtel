<?php

class m130409_141329_add_ticket_created_field extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `ticket`
            ADD `created_by` varchar(200) NOT NULL COMMENT 'кто создал тикет' AFTER `number_id`,
            COMMENT='Хранит тикеты';
        ");
        $this->execute("
            ALTER TABLE `ticket`
            ADD INDEX `created_by` (`created_by`);
        ");
	}

	public function down()
	{
		echo "m130409_141329_add_ticket_created_field does not support migration down.\n";
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