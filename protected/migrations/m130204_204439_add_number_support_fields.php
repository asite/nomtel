<?php

class m130204_204439_add_number_support_fields extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `number`
            ADD `support_operator_id` int(11) NULL,
            ADD `support_dt` timestamp NULL AFTER `support_operator_id`,
            ADD `support_status` enum('UNAVAILABLE','CALLBACK','REJECT','ACTIVE','SERVICE_INFO') COLLATE 'utf8_general_ci' NULL AFTER `support_dt`,
            ADD `support_callback_dt` timestamp NULL AFTER `support_status`,
            ADD `support_callback_name` varchar(200) COLLATE 'utf8_general_ci' NULL AFTER `support_callback_dt`,
            ADD FOREIGN KEY (`support_operator_id`) REFERENCES `support_operator` (`id`),
            COMMENT='';
        ");
	}

	public function down()
	{
		echo "m130204_204439_add_number_support_fields does not support migration down.\n";
		return false;
	}
}