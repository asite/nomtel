<?php

class m130816_085354_rename_ask_for_sum_to_sim_given extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `megafon_app_restore_number`
            CHANGE `ask_for_sum` `sim_given` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'SIM отдана клиенту' AFTER `cashier_debit_credit_id`,
            COMMENT='Мегафоновские номера на восстановление';
        ");
	}

	public function down()
	{
		echo "m130816_085354_rename_ask_for_sum_to_sim_given does not support migration down.\n";
		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}