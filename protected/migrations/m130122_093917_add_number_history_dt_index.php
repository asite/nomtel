<?php

class m130122_093917_add_number_history_dt_index extends CDbMigration
{
	public function up()
	{
        $this->db->execute("
            ALTER TABLE `number_history`
            ADD INDEX `dt` (`dt`);
        ");
	}

	public function down()
	{
		echo "m130122_093917_add_hunber_history_dt_index does not support migration down.\n";
		return false;
	}
}