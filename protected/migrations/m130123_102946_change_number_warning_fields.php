<?php

class m130123_102946_change_number_warning_fields extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `number`
            CHANGE `warning` `balance_status` enum('NORMAL','POSITIVE_STATIC','NEGATIVE_STATIC','POSITIVE_DYNAMIC','NEGATIVE_DYNAMIC','NEW','MISSING') NOT NULL DEFAULT 'NORMAL' AFTER `status`,
            CHANGE `warning_dt` `balance_status_changed_dt` timestamp NULL AFTER `balance_status`,
            COMMENT='';
        ");

        $this->execute("
            update number set balance_status='NORMAL',balance_status_changed_dt=NOW()
        ");
    }

	public function down()
	{
		echo "m130123_102946_change_number_warning_fields does not support migration down.\n";
		return false;
	}
}