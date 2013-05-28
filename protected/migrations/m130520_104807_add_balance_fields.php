<?php

class m130520_104807_add_balance_fields extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `number`
            ADD `balance` decimal(14,2) NULL COMMENT 'текущий баланс номера',
            ADD `balance_changed_dt` timestamp NULL COMMENT 'дата последнего изменения баланса' AFTER `balance`,
            COMMENT='Хранит данные по номеру';
        ");
	}

	public function down()
	{
		echo "m130520_104807_add_balance_fields does not support migration down.\n";
		return false;
	}
}