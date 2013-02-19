<?php

class m130219_201826_balance_report_add_dt_index extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `balance_report`
            ADD UNIQUE `dt` (`dt`);
        ");
	}

	public function down()
	{
		echo "m130219_201826_balance_report_add_dt_index does not support migration down.\n";
		return false;
	}
}