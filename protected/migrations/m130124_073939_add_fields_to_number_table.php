<?php

class m130124_073939_add_fields_to_number_table extends CDbMigration
{
    public function safeUp()
	{
		$this->execute("ALTER TABLE `number`
						ADD `codeword` varchar(20) COLLATE 'utf8_general_ci' NULL,
						ADD `service_password` varchar(20) COLLATE 'utf8_general_ci' NULL AFTER `codeword`,
						COMMENT=''");
	}

	public function safeDown()
	{
		echo "m130124_073939_add_fields_to_number_table does not support migration down.\n";
		return false;
	}
}