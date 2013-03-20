<?php

class m130320_205316_add_blank_sim_table extends CDbMigration
{
	public function up()
	{
        $this->execute("
            CREATE TABLE `blank_sim` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `type` enum('NORMAL','MICRO','NANO') NOT NULL COMMENT 'тип симки',
              `icc` varchar(50) NOT NULL COMMENT 'icc',
              `operator_id` int(11) NOT NULL COMMENT 'оператор',
              `operator_region_id` int(11) NOT NULL COMMENT 'регион',
              `used_dt` timestamp NULL DEFAULT NULL COMMENT 'дата и время, когда симка была использована для восстановления',
              `used_support_operator_id` int(11) DEFAULT NULL COMMENT 'оператор, который осуществил восстановление',
              `used_number_id` bigint(20) DEFAULT NULL COMMENT 'номер, который был восстановлен',
              PRIMARY KEY (`id`),
              KEY `operator_id` (`operator_id`),
              KEY `operator_region_id` (`operator_region_id`),
              KEY `used_number_id` (`used_number_id`),
              KEY `used_support_operator_id` (`used_support_operator_id`),
              CONSTRAINT `blank_sim_ibfk_5` FOREIGN KEY (`used_support_operator_id`) REFERENCES `support_operator` (`id`),
              CONSTRAINT `blank_sim_ibfk_1` FOREIGN KEY (`operator_id`) REFERENCES `operator` (`id`),
              CONSTRAINT `blank_sim_ibfk_2` FOREIGN KEY (`operator_region_id`) REFERENCES `operator_region` (`id`),
              CONSTRAINT `blank_sim_ibfk_4` FOREIGN KEY (`used_number_id`) REFERENCES `number` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Хранит информацию о SIM-пустышках';
        ");
    }

	public function down()
	{
		echo "m130320_205316_add_blank_sim_table does not support migration down.\n";
		return false;
	}
}