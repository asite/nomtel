<?php

class m130728_202709_change_number_balance_statuses extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `number`
            CHANGE `balance_status` `balance_status` enum('CHANGING','NOT_CHANGING','NO_DATA','CLOSED') COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'NO_DATA' COMMENT 'статус баланса' AFTER `status`,
            COMMENT='Хранит данные по номеру';
        ");

        $this->execute("update number set balance_status='NO_DATA',balance_status_changed_dt=NULL");
	}

	public function down()
	{
		echo "m130728_202709_change_number_balance_statuses does not support migration down.\n";
		return false;
	}
}