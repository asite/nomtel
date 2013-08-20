<?php

class m130820_074807_modify_cashier_tables extends CDbMigration
{
	public function up()
	{
        $this->execute("delete from `megafon_app_restore_number`");
        $this->execute("delete from `megafon_app_restore`");
        $this->execute("delete from `cashier_collection`");
        $this->execute("delete from `cashier_sell_number`");
        $this->execute("delete from `cashier_debit_credit`");

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