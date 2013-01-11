<?php

class m130111_063205_number_number_is_unique extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `number`
            ADD UNIQUE `number` (`number`),
            DROP INDEX `personal_account_number`;
        ");
	}

	public function down()
	{
		echo "m130111_063205_number_number_is_unique does not support migration down.\n";
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