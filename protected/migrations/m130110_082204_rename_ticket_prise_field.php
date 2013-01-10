<?php

class m130110_082204_rename_ticket_prise_field extends CDbMigration
{
	public function safeUp()
	{
		$this->execute("
            ALTER TABLE `ticket`
            CHANGE `prise` `price` decimal(14,2) NULL DEFAULT '0.00' AFTER `status`,
            COMMENT='';
        ");
	}

	public function safeDown()
	{
		echo "m130110_082204_rename_ticket_prise_field does not support migration down.\n";
		return false;
	}
}