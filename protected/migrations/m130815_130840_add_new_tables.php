<?php

class m130815_130840_add_new_tables extends CDbMigration
{
	public function up()
	{
        $this->execute("
CREATE TABLE `cashier_debit_credit` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'дата и время',
  `support_operator_id` int(11) NOT NULL COMMENT 'кассир',
  `comment` varchar(200) NOT NULL COMMENT 'комментарий',
  `sum` decimal(14,2) NOT NULL COMMENT 'сумма операции',
  PRIMARY KEY (`id`),
  KEY `support_operator_id` (`support_operator_id`),
  CONSTRAINT `cashier_debit_credit_ibfk_1` FOREIGN KEY (`support_operator_id`) REFERENCES `support_operator` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Приход/расход по кассе';
        ");

        $this->execute("
CREATE TABLE `megafon_app_restore` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dt` date NOT NULL COMMENT 'дата заявления',
  `numbers_count` int(11) NOT NULL DEFAULT '0' COMMENT 'кол-во номеров в заявлении',
  `unprocessed_numbers_count` int(11) NOT NULL DEFAULT '0' COMMENT 'кол-во необработанных номеров в заявлении',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Заявления на восстановление мегафоновских номеров';
        ");

        $this->execute("
CREATE TABLE `megafon_app_restore_number` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `megafon_app_restore_id` int(11) NOT NULL COMMENT 'заявление на восстановление',
  `number_id` bigint(20) NOT NULL COMMENT 'номер телефона',
  `support_operator_id` int(11) DEFAULT NULL COMMENT 'кассир, который продал/восстановил карту',
  `sim_type` enum('NORMAL','MICRO','NANO') NOT NULL COMMENT 'тип симки',
  `status` enum('PROCESSING','DONE','REJECTED') NOT NULL COMMENT 'статус восстановления',
  `cashier_debit_credit_id` bigint(20) DEFAULT NULL COMMENT 'дата и время платежа',
  `contact_phone` varchar(20) DEFAULT NULL COMMENT 'контактный телефон',
  `contact_name` varchar(100) DEFAULT NULL COMMENT 'контактное лицо',
  PRIMARY KEY (`id`),
  KEY `number_id` (`number_id`),
  KEY `megafon_app_restore_id` (`megafon_app_restore_id`),
  KEY `support_operator_id` (`support_operator_id`),
  KEY `cashier_debit_credit_id` (`cashier_debit_credit_id`),
  CONSTRAINT `megafon_app_restore_number_ibfk_4` FOREIGN KEY (`cashier_debit_credit_id`) REFERENCES `cashier_debit_credit` (`id`),
  CONSTRAINT `megafon_app_restore_number_ibfk_1` FOREIGN KEY (`number_id`) REFERENCES `number` (`id`),
  CONSTRAINT `megafon_app_restore_number_ibfk_2` FOREIGN KEY (`megafon_app_restore_id`) REFERENCES `megafon_app_restore` (`id`),
  CONSTRAINT `megafon_app_restore_number_ibfk_3` FOREIGN KEY (`support_operator_id`) REFERENCES `support_operator` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Мегафоновские номера на восстановление';
        ");
    }

	public function down()
	{
		echo "m130815_130840_add_new_tables does not support migration down.\n";
		return false;
	}
}