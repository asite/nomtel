<?php

class m130219_133337_add_number_field_support_operator_got_dt extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `number`
			ADD `support_operator_got_dt` timestamp NULL AFTER `support_operator_id`,
			COMMENT='';
        ");
	}

	public function safeDown()
	{
		echo "m130219_133337_add_number_field_support_operator_got_dt does not support migration down.\n";
		return false;
	}
}

