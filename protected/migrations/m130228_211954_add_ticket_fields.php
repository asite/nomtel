<?php

class m130228_211954_add_ticket_fields extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `ticket`
            ADD `internal` mediumtext COLLATE 'utf8_general_ci' NULL,
            ADD `response` mediumtext COLLATE 'utf8_general_ci' NULL AFTER `internal`,
            COMMENT='';
        ");
	}

	public function down()
	{
		echo "m130228_211954_add_ticket_fields does not support migration down.\n";
		return false;
	}
}