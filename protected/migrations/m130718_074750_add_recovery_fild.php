<?php

class m130718_074750_add_recovery_fild extends CDbMigration
{
	public function up()
	{
	    $this->execute("
                ALTER TABLE `number`
            ADD `recovery_dt` timestamp NULL COMMENT 'дата последнеговосстановления' ;
           
        ");
	}

	public function down()
	{
		echo "m130718_074750_add_recovery_fild does not support migration down.\n";
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