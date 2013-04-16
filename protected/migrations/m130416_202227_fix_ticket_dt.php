<?php

class m130416_202227_fix_ticket_dt extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `ticket`
            CHANGE `dt` `dt` timestamp NOT NULL COMMENT 'дата создания тикета' AFTER `support_operator_id`,
            COMMENT='Хранит тикеты';
        ");
        $this->execute("
            update ticket
            join (select ticket_id,min(dt) as dt from ticket_history
            group by ticket_id) as t on (t.ticket_id=ticket.id) set ticket.dt=t.dt
        ");
	}

	public function down()
	{
		echo "m130416_202227_fix_ticket_dt does not support migration down.\n";
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