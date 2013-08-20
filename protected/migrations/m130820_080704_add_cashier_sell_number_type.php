<?php

class m130820_080704_add_cashier_sell_number_type extends CDbMigration
{
	public function up()
	{
        $this->execute("
ALTER TABLE `cashier_sell_number`
ADD `type` enum('AGENT','CLIENT') NOT NULL AFTER `support_operator_id`,
COMMENT='Номера, проданные/восстановленные кассиром';
        ");
	}

	public function down()
	{
		echo "m130820_080704_add does not support migration down.\n";
		return false;
	}
}