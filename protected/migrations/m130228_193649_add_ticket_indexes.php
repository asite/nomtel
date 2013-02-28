<?php

class m130228_193649_add_ticket_indexes extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `ticket`
            ADD INDEX `status` (`status`);
        ");
    }

	public function down()
	{
		echo "m130228_193649_add_ticket_indexes does not support migration down.\n";
		return false;
	}
}