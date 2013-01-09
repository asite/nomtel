<?php

class m130109_142828_rename_agent_birth_fields extends CDbMigration
{
	public function safeUp()
	{
        $this->execute("
            ALTER TABLE `agent`
            CHANGE `birthday_date` `birth_date` date NOT NULL AFTER `passport_issuer`,
            CHANGE `birthday_place` `birth_place` varchar(200) COLLATE 'utf8_general_ci' NOT NULL AFTER `birth_date`,
            COMMENT='';
        ");
    }

	public function safeDown()
	{
		echo "m130109_142828_rename_agent_birth_fields does not support migration down.\n";
		return false;
	}
}