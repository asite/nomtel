<?php

class m130219_151727_update_support_sent_sms_status extends CDbMigration
{
	public function safeUp()
	{
		$this->execute("UPDATE `number` SET `support_sent_sms_status`='OFFICE' WHERE `support_sent_sms_address`=1;");
        $this->execute("UPDATE `number` SET `support_sent_sms_status`='EMAIL' WHERE `support_sent_sms_email`=1;");
	}

	public function safeDown()
	{
		echo "m130219_151727_update_support_sent_sms_status does not support migration down.\n";
		return false;
	}
}

