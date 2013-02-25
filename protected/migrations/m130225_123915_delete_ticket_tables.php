<?php

class m130225_123915_delete_ticket_tables extends CDbMigration
{
	public function up()
	{
        $this->execute("DROP TABLE `ticket_message`;");
        $this->execute("DROP TABLE `ticket`;");
	}

	public function down()
	{
		echo "m130225_123915_delete_ticket_tables does not support migration down.\n";
		return false;
	}
}