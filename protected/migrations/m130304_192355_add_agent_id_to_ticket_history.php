<?php

class m130304_192355_add_agent_id_to_ticket_history extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `ticket_history`
            CHANGE `support_operator_id` `support_operator_id` int(11) NULL AFTER `dt`,
            ADD `agent_id` int(11) NULL AFTER `support_operator_id`,
            ADD FOREIGN KEY (`agent_id`) REFERENCES `agent` (`id`),
            COMMENT='';
        ");
	}

	public function down()
	{
		echo "m130304_192355_add_agent_id_to_ticket_history does not support migration down.\n";
		return false;
	}
}