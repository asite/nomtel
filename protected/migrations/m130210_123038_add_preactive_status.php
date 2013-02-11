<?php

class m130210_123038_add_preactive_status extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `number`
            CHANGE `support_status` `support_status` enum('UNAVAILABLE','CALLBACK','REJECT','PREACTIVE','ACTIVE','SERVICE_INFO') COLLATE 'utf8_general_ci' NULL AFTER `support_dt`,
            COMMENT='';
        ");
    }

	public function down()
	{
		echo "m130210_123038_add_preactive_status does not support migration down.\n";
		return false;
	}
}