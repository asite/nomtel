<?php

class m130122_074830_modify_balance_report_number extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `balance_report_number`
            ADD INDEX `balance_report_id` (`balance_report_id`),
            DROP INDEX `PRIMARY`;
        ");
        $this->execute("
            ALTER TABLE `balance_report_number`
            ADD `id` bigint NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
            COMMENT='';
        ");
	}

	public function down()
	{
		echo "m130122_074830_modify_balance_report_number does not support migration down.\n";
		return false;
	}
}