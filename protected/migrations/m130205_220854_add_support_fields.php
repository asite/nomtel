<?php

class m130205_220854_add_support_fields extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `number`
            ADD `support_getting_passport_variant` varchar(200) COLLATE 'utf8_general_ci' NULL,
            ADD `support_number_region_usage` varchar(200) COLLATE 'utf8_general_ci' NULL AFTER `support_getting_passport_variant`,
            COMMENT='';
        ");
	}

	public function down()
	{
		echo "m130205_220854_add_support_fields does not support migration down.\n";
		return false;
	}
}