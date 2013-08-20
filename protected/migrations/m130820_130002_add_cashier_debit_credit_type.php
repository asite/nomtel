<?php

class m130820_130002_add_cashier_debit_credit_type extends CDbMigration
{
	public function up()
	{
        $this->execute("
ALTER TABLE `cashier_debit_credit`
ADD `type` enum('NUMBER_SELL','NUMBER_RESTORE','NUMBER_RESTORE_REJECT','COLLECTION','OTHER') NOT NULL AFTER `dt`,
COMMENT='Приход/расход по кассе';
        ");
	}

	public function down()
	{
		echo "m130820_130002_add_cashier_debit_credit_type does not support migration down.\n";
		return false;
	}
}