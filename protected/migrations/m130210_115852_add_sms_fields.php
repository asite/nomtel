<?php

class m130210_115852_add_sms_fields extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `number`
            ADD `support_sent_sms_address` tinyint NOT NULL DEFAULT '0',
            ADD `support_sent_sms_email` tinyint NOT NULL DEFAULT '0' AFTER `support_sent_sms_address`,
            COMMENT='';
        ");
	}

	public function down()
	{
		echo "m130210_115852_add_sms_fields does not support migration down.\n";
		return false;
	}
}