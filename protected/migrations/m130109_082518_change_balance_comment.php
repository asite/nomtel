<?php

class m130109_082518_change_balance_comment extends CDbMigration
{
	public function safeUp()
	{
        $this->execute("
            ALTER TABLE `balance_report_number`
            CHANGE `balance` `balance` decimal(14,2) NOT NULL COMMENT 'для мегафона баланс, для билайна - выручка' AFTER `number_id`,
            COMMENT='';
        ");
	}

	public function safeDown()
	{
		echo "m130109_082518_change_balance_comment does not support migration down.\n";
		return false;
	}
}