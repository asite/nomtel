<?php

class m130315_140458_add_sim_active_field extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `sim`
            ADD `is_active` tinyint NULL DEFAULT '1' COMMENT '0 - симка не активна (была восстановлена на другую)',
            COMMENT='Хранит информацию о симках и их передаче между агентами. Если симка прошла через N агентов (включая базу), то в таблице будет N записей для этой симки';
        ");
    }

	public function down()
	{
		echo "m130315_140458_add_sim_active_field does not support migration down.\n";
		return false;
	}
}