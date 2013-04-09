<?php

class m130408_205341_fix_agent_parent_foreign_key extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `agent`
            DROP FOREIGN KEY `agent_ibfk_2`,
            ADD FOREIGN KEY (`parent_id`) REFERENCES `agent` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
        ");
	}

	public function down()
	{
		echo "m130408_205341_fix_agent_parent_foreign_key does not support migration down.\n";
		return false;
	}
}