<?php

class m130301_130555_add_ticket_refused_status extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `ticket`
            CHANGE `status` `status` enum('NEW','IN_WORK_MEGAFON','IN_WORK_OPERATOR','REFUSED_BY_MEGAFON','REFUSED_BY_ADMIN','REFUSED_BY_OPERATOR','FOR_REVIEW','DONE','REFUSED') COLLATE 'utf8_general_ci' NOT NULL AFTER `dt`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `ticket_history`
            CHANGE `status` `status` enum('NEW','IN_WORK_MEGAFON','IN_WORK_OPERATOR','REFUSED_BY_MEGAFON','REFUSED_BY_ADMIN','REFUSED_BY_OPERATOR','FOR_REVIEW','DONE','REFUSED') COLLATE 'utf8_general_ci' NOT NULL AFTER `comment`,
            COMMENT='';
        ");
	}

	public function down()
	{
		echo "m130301_130555_add_ticket_refused_status does not support migration down.\n";
		return false;
	}
}