<?php

class m130225_130009_add_ticket_tables extends CDbMigration
{
	public function up()
	{
        $this->execute("
          CREATE TABLE `ticket` (
          `id` bigint NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `number_id` bigint(20) NOT NULL,
          `sim_id` bigint(20) NOT NULL,
          `agent_id` int(11) NOT NULL,
          `support_operator_id` int(11) NULL,
          `dt` timestamp NOT NULL,
          `status` enum('NEW','IN_WORK_MEGAFON','IN_WORK_OPERATOR','REFUSED_BY_MEGAFON','REFUSED_BY_ADMIN','REFUSED_BY_OPERATOR','FOR_REVIEW','DONE') NOT NULL,
          `text` mediumtext  COLLATE 'utf8_general_ci' NOT NULL,
          FOREIGN KEY (`number_id`) REFERENCES `number` (`id`),
          FOREIGN KEY (`sim_id`) REFERENCES `sim` (`id`),
          FOREIGN KEY (`agent_id`) REFERENCES `agent` (`id`),
          FOREIGN KEY (`support_operator_id`) REFERENCES `support_operator` (`id`)
        ) COMMENT='';
        ");

        $this->execute("
            CREATE TABLE `ticket_history` (
            `id` bigint NOT NULL,
            `ticket_id` bigint(20) NOT NULL,
            `dt` timestamp NOT NULL,
            `support_operator_id` int(11) NOT NULL,
            `comment` mediumtext NOT NULL,
            `status` enum('NEW','IN_WORK_MEGAFON','IN_WORK_OPERATOR','REFUSED_BY_MEGAFON','REFUSED_BY_ADMIN','REFUSED_BY_OPERATOR','FOR_REVIEW','DONE') NOT NULL,
            FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`id`),
            FOREIGN KEY (`support_operator_id`) REFERENCES `support_operator` (`id`)
            ) COMMENT='';
        ");
	}

	public function down()
	{
		echo "m130225_130009_add_ticket_tables does not support migration down.\n";
		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}