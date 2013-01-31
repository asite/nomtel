<?php

class m130128_183500_add_bonus_report_number_table extends CDbMigration
{
	public function up()
	{
        $this->execute("
            CREATE TABLE `bonus_report_number` (
              `id` bigint NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `bonus_report_id` int(11) NOT NULL,
              `number_id` bigint(20) NOT NULL,
              `agent_id` int(11) NOT NULL,
              `turnover` decimal(14,2) NULL,
              `rate` decimal(5,2) NOT NULL,
              `sum` decimal(14,2) NULL,
              `status` enum('OK','TURNOVER_ZERO','NUMBER_MISSING') NOT NULL,
              FOREIGN KEY (`bonus_report_id`) REFERENCES `bonus_report` (`id`),
              FOREIGN KEY (`number_id`) REFERENCES `number` (`id`),
              FOREIGN KEY (`agent_id`) REFERENCES `agent` (`id`)
            ) COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `bonus_report_number`
            CHANGE `agent_id` `parent_agent_id` int(11) NOT NULL AFTER `number_id`,
            ADD `agent_id` int(11) NOT NULL AFTER `parent_agent_id`,
            ADD FOREIGN KEY (`parent_agent_id`) REFERENCES `agent` (`id`),
            ADD FOREIGN KEY (`agent_id`) REFERENCES `agent` (`id`),
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `bonus_report_number`
            CHANGE `agent_id` `agent_id` int(11) NULL AFTER `parent_agent_id`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `bonus_report_number`
            CHANGE `rate` `rate` decimal(5,2) NULL AFTER `turnover`,
            COMMENT='';
        ");
	}

	public function down()
	{
		echo "m130128_183500_add_bonus_report_number_table does not support migration down.\n";
		return false;
	}
}