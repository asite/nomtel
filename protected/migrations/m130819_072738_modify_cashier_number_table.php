<?php

class m130819_072738_modify_cashier_number_table extends CDbMigration
{
	public function up()
	{
        $this->execute("
ALTER TABLE `cashier_number`
DROP `type`,
DROP `ticket_id`,
DROP `sum_cashier`,
CHANGE `sum` `sum` decimal(14,2) NOT NULL COMMENT 'сумма' AFTER `number_id`,
ADD `cashier_debit_credit_id` bigint(20) NULL AFTER `sum`,
DROP `confirmed`,
ADD `comment` varchar(200) NULL COMMENT 'комментарий при безнал. оплате',
ADD FOREIGN KEY (`cashier_debit_credit_id`) REFERENCES `cashier_debit_credit` (`id`),
RENAME TO `cashier_sell_number`,
COMMENT='Номера, проданные/восстановленные кассиром';
        ");
	}

	public function down()
	{
		echo "m130819_072738_modify_cashier_number_table does not support migration down.\n";
		return false;
	}
}