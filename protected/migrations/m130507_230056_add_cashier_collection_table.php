<?php

class m130507_230056_add_cashier_collection_table extends CDbMigration
{
	public function up()
	{
        $this->execute("
            CREATE TABLE `cashier_collection` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'дата проведения инкассации',
              `cashier_support_operator_id` int(11) NOT NULL COMMENT 'кассир, кассу которого проинкассировали',
              `collector_support_operator_id` int(11) NOT NULL COMMENT 'кто провел инкассацию',
              `sum` decimal(14,2) NOT NULL COMMENT 'сумма инкассации',
              PRIMARY KEY (`id`),
              KEY `cashier_support_operator_id` (`cashier_support_operator_id`),
              KEY `collector_support_operator_id` (`collector_support_operator_id`),
              CONSTRAINT `cashier_collection_ibfk_1` FOREIGN KEY (`cashier_support_operator_id`) REFERENCES `support_operator` (`id`),
              CONSTRAINT `cashier_collection_ibfk_2` FOREIGN KEY (`collector_support_operator_id`) REFERENCES `support_operator` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Инкассация';
        ");
	}

	public function down()
	{
		echo "m130507_230056_add_cashier_collection_table does not support migration down.\n";
		return false;
	}
}