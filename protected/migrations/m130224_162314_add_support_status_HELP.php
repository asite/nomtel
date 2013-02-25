<?php

class m130224_162314_add_support_status_HELP extends CDbMigration
{
	public function safeUp()
	{
		$this->execute("ALTER TABLE `number`
						CHANGE `support_status` `support_status` enum('UNAVAILABLE','CALLBACK','REJECT','PREACTIVE','ACTIVE','SERVICE_INFO','HELP') COLLATE 'utf8_general_ci' NULL AFTER `support_dt`,
						COMMENT='';"
						);
	}

	public function safeDown()
	{
		echo "m130224_162314_add_support_status_HELP does not support migration down.\n";
		return false;
	}
}