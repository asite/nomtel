<?php

class m130219_151041_add_field_support_sent_sms_status extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `number`
			ADD `support_sent_sms_status` enum('OFFICE','LK','EMAIL') COLLATE 'utf8_general_ci' NULL AFTER `support_sent_sms_email`,
			COMMENT='';
        ");
	}

	public function safeDown()
	{
		echo "m130219_151041_add_field_support_sent_sms_status does not support migration down.\n";
		return false;
	}
}