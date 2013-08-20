<?php

class m130820_074807_modify_cashier_tables extends CDbMigration
{
	public function up()
	{
        $this->execute("TRUNCATE TABLE `cashier_collection`");
        $this->execute("TRUNCATE TABLE `cashier_sell_number`");
        $this->execute("TRUNCATE TABLE `cashier_debit_credit`");

        $this->execute("
ALTER TABLE `cashier_collection`
ADD `cashier_debit_credit_id` bigint(20) NOT NULL,
ADD FOREIGN KEY (`cashier_debit_credit_id`) REFERENCES `cashier_debit_credit` (`id`),
COMMENT='Инкассация';");
	}

	public function down()
	{
		echo "m130820_074807_modify_cashier_tables does not support migration down.\n";
		return false;
	}
}