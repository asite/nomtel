<?php

class m130429_064803_add_two_sums_for_sale extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `cashier_number`
            CHANGE `sum` `sum_cashier` decimal(14,2) NOT NULL COMMENT 'сумма, зачисленная кассиру' AFTER `ticket_id`,
            ADD `sum_nomtel` decimal(14,2) NOT NULL COMMENT 'доход' AFTER `sum_cashier`,
            COMMENT='Номера, проданные/восстановленные кассиром';
        ");

        $this->execute("
            ALTER TABLE `cashier_number`
            CHANGE `sum_cashier` `sum_cashier` decimal(14,2) NOT NULL COMMENT 'доход кассира' AFTER `ticket_id`,
            CHANGE `sum_nomtel` `sum` decimal(14,2) NOT NULL COMMENT 'сумма в кассу' AFTER `sum_cashier`,
            COMMENT='Номера, проданные/восстановленные кассиром';
        ");
	}

	public function down()
	{
		echo "m130429_064803_add_two_sums_for_sale does not support migration down.\n";
		return false;
	}
}