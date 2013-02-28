<?php

class m130228_210340_modify_ticket_history_table extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `ticket_history`
            CHANGE `id` `id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
            CHANGE `comment` `comment` mediumtext COLLATE 'utf8_general_ci' NULL AFTER `support_operator_id`,
            COMMENT='';
        ");
	}

	public function down()
	{
		echo "m130228_210340_modify_ticket_history_table does not support migration down.\n";
		return false;
	}
}