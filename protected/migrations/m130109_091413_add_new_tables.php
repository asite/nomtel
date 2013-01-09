<?php

class m130109_091413_add_new_tables extends CDbMigration
{
	public function safeUp()
	{
        $this->execute("
            CREATE TABLE `file` (
                    `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `url` varchar(200) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->execute("
            CREATE TABLE `number_history` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `number_id` bigint(20) NOT NULL,
              `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `who` varchar(200) NOT NULL,
              `comment` varchar(200) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `number_id` (`number_id`),
              CONSTRAINT `number_history_ibfk_1` FOREIGN KEY (`number_id`) REFERENCES `number` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->execute("
            CREATE TABLE `person` (
                    `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `name` varchar(200) NOT NULL,
              `surname` varchar(200) NOT NULL,
              `middle_name` varchar(200) NOT NULL,
              `phone` varchar(200) DEFAULT NULL,
              `email` varchar(200) DEFAULT NULL,
              `passport_series` varchar(10) NOT NULL,
              `passport_number` varchar(20) NOT NULL,
              `passport_issue_date` date NOT NULL,
              `passport_issuer` varchar(200) NOT NULL,
              `birth_date` date NOT NULL,
              `birth_place` varchar(200) NOT NULL,
              `registration_address` varchar(200) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->execute("
            CREATE TABLE `person_file` (
                    `person_id` bigint(20) NOT NULL,
              `file_id` bigint(20) NOT NULL,
              PRIMARY KEY (`person_id`,`file_id`),
              KEY `file_id` (`file_id`),
              CONSTRAINT `person_file_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
              CONSTRAINT `person_file_ibfk_2` FOREIGN KEY (`file_id`) REFERENCES `file` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->execute("
            CREATE TABLE `subscription_agreement` (
                    `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `defined_id` varchar(50) NOT NULL,
              `number_id` bigint(20) NOT NULL,
              `person_id` bigint(20) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `number_id` (`number_id`),
              KEY `person_id` (`person_id`),
              CONSTRAINT `subscription_agreement_ibfk_1` FOREIGN KEY (`number_id`) REFERENCES `number` (`id`),
              CONSTRAINT `subscription_agreement_ibfk_3` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->execute("
            CREATE TABLE `subscription_agreement_file` (
                    `subscription_agreement_id` bigint(20) NOT NULL,
              `file_id` bigint(20) NOT NULL,
              PRIMARY KEY (`subscription_agreement_id`,`file_id`),
              KEY `file_id` (`file_id`),
              CONSTRAINT `subscription_agreement_file_ibfk_1` FOREIGN KEY (`subscription_agreement_id`) REFERENCES `subscription_agreement` (`id`),
              CONSTRAINT `subscription_agreement_file_ibfk_2` FOREIGN KEY (`file_id`) REFERENCES `file` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->execute("
            CREATE TABLE `support_operator` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `user_id` int(11) NOT NULL,
              `name` varchar(200) NOT NULL,
              `surname` varchar(200) NOT NULL,
              `middle_name` varchar(200) NOT NULL,
              `phone` varchar(200) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `user_id` (`user_id`),
              CONSTRAINT `support_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->execute("
            ALTER TABLE `number`
            ADD `sim_id` bigint(20) NULL AFTER `id`,
            CHANGE `status` `status` enum('UNKNOWN','FREE','CONNECTED') COLLATE 'utf8_general_ci' NULL AFTER `personal_account`,
            CHANGE `warning_dt` `warning` tinyint(1) NULL AFTER `status`,
            ADD `warning_dt` timestamp NULL,
            ADD FOREIGN KEY (`sim_id`) REFERENCES `sim` (`id`),
            COMMENT='';
        ");
    }

	public function safeDown()
	{
		echo "m130109_091413_add_new_tables does not support migration down.\n";
		return false;
	}
}