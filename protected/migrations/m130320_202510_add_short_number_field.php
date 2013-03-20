<?php

class m130320_202510_add_short_number_field extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `number`
            ADD `short_number` varchar(20) COLLATE 'utf8_general_ci' NULL AFTER `service_password`,
            COMMENT='Хранит данные по номеру';
        ");

        $this->execute("
            ALTER TABLE `number`
            CHANGE `short_number` `short_number` varchar(20) COLLATE 'utf8_general_ci' NULL COMMENT 'короткий номер' AFTER `service_password`,
            COMMENT='Хранит данные по номеру';
        ");
	}

	public function down()
	{
		echo "m130320_202510_add_short_number_field does not support migration down.\n";
		return false;
	}
}