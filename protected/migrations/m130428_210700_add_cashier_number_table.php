<?php

class m130428_210700_add_cashier_number_table extends CDbMigration
{
	public function up()
	{
        $this->execute("
            CREATE TABLE `cashier_number` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'дата восстановления/продажи',
              `support_operator_id` int(11) NOT NULL COMMENT 'кассир',
              `number_id` bigint(20) NOT NULL COMMENT 'номер',
              `type` enum('SELL','RESTORE') NOT NULL COMMENT 'тип операции',
              `ticket_id` bigint(20) NOT NULL COMMENT 'тикет мегафона',
              `sum` decimal(14,2) NOT NULL COMMENT 'сумма, зачисленная кассиру',
              `confirmed` tinyint(4) NOT NULL DEFAULT '0' COMMENT '=1 когда операция обработана мегафоном и кассиру можно зачислить деньги',
              PRIMARY KEY (`id`),
              KEY `support_operator_id` (`support_operator_id`),
              KEY `number_id` (`number_id`),
              KEY `ticket_id` (`ticket_id`),
              CONSTRAINT `cashier_number_ibfk_1` FOREIGN KEY (`support_operator_id`) REFERENCES `support_operator` (`id`),
              CONSTRAINT `cashier_number_ibfk_2` FOREIGN KEY (`number_id`) REFERENCES `number` (`id`),
              CONSTRAINT `cashier_number_ibfk_3` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Номера, проданные/восстановленные кассиром';
        ");
	}

	public function down()
	{
		echo "m130428_210700_add_cashier_number_table does not support migration down.\n";
		return false;
	}
}