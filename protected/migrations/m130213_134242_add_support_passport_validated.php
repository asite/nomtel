<?php

class m130213_134242_add_support_passport_validated extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `number`
            ADD `support_passport_need_validation` tinyint NOT NULL DEFAULT '0',
            COMMENT='';
        ");
	}

	public function down()
	{
		echo "m130213_134242_add_support_passport_validated does not support migration down.\n";
		return false;
	}
}