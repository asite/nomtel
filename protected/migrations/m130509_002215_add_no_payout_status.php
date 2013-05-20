<?php

class m130509_002215_add_no_payout_status extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `bonus_report_number`
            CHANGE `status` `status` enum('OK','TURNOVER_ZERO','NUMBER_MISSING','NO_PAYOUT') COLLATE 'utf8_general_ci' NOT NULL COMMENT 'статус: ОК- все хорошо, TURNOVER_ZERO - нулевой оборот по номеру, NUMBER_MISSING- номер уже присутствовал в предыдущих отчетах, но в текущем отсутствует, NO_PAYOUT - выплата не положена (тариф территория)' AFTER `sum`,
            COMMENT='Данные по бонусному отчету по номерам в разрезе всех агентов (для кадного номера хранится столько записей, через скольких агентов к нему попала симка)';
        ");
	}

	public function down()
	{
		echo "m130509_002215_add_no_payout_status does not support migration down.\n";
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