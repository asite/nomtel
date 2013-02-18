<?php

class m130218_213825_last_password_restore_field extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `user`
            ADD `last_password_restore` timestamp NULL,
            COMMENT='';
        ");
	}

	public function down()
	{
		echo "m130218_213825_last_password_restore_field does not support migration down.\n";
		return false;
	}
}