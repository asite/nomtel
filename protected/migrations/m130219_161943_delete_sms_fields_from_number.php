<?php

class m130219_161943_delete_sms_fields_from_number extends CDbMigration
{
	public function safeUp()
	{
		$this->execute("ALTER TABLE `number` DROP `support_sent_sms_address`, DROP `support_sent_sms_email`, COMMENT='';");
	}

	public function safeDown()
	{
		echo "m130219_161943_delete_sms_fields_from_number does not support migration down.\n";
		return false;
	}
}