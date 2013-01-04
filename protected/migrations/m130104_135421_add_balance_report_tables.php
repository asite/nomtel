<?php

class m130104_135421_add_balance_report_tables extends CDbMigration
{
	public function safeUp()
	{
        $this->execute("
            CREATE TABLE `balance_report` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `operator_id` int(11) NOT NULL,
              `comment` varchar(200) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `operator_id` (`operator_id`),
              CONSTRAINT `balance_report_ibfk_1` FOREIGN KEY (`operator_id`) REFERENCES `operator` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->execute("
          CREATE TABLE `number` (
          `id` bigint(20) NOT NULL AUTO_INCREMENT,
          `number` varchar(50) NOT NULL,
          `personal_account` varchar(50) NOT NULL,
          `status` enum('NORMAL','WARNING') NOT NULL,
          `warning_dt` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `personal_account_number` (`personal_account`,`number`),
          KEY `warning` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->execute("
          CREATE TABLE `balance_report_number` (
          `balance_report_id` int(11) NOT NULL,
          `number_id` bigint(20) NOT NULL,
          `balance` decimal(18,2) NOT NULL,
          PRIMARY KEY (`balance_report_id`,`number_id`),
          KEY `number_id` (`number_id`),
          CONSTRAINT `balance_report_number_ibfk_1` FOREIGN KEY (`balance_report_id`) REFERENCES `balance_report` (`id`),
          CONSTRAINT `balance_report_number_ibfk_2` FOREIGN KEY (`number_id`) REFERENCES `number` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

	public function safeDown()
	{
		echo "m130104_135421_add_balance_report_tables does not support migration down.\n";
		return false;
	}
}