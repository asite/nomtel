<?php

class m130122_190302_number_status_connected_to_active extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `number`
            CHANGE `status` `status` enum('UNKNOWN','FREE','CONNECTED','ACTIVE','BLOCKED') COLLATE 'utf8_general_ci' NULL AFTER `personal_account`,
            COMMENT='';
        ");

        $this->execute("
            update number set status='ACTIVE' where status='CONNECTED'
        ");

        $this->execute("
            ALTER TABLE `number`
            CHANGE `status` `status` enum('UNKNOWN','FREE','ACTIVE','BLOCKED') COLLATE 'utf8_general_ci' NULL AFTER `personal_account`,
            COMMENT='';
        ");
    }

	public function down()
	{
		echo "m130122_200302_number_status_connected_to_active does not support migration down.\n";
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