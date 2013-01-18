<?php

class m130118_141056_number_warning_is_required extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `number`
            CHANGE `warning` `warning` tinyint(1) NOT NULL DEFAULT '0' AFTER `status`,
            COMMENT='';
        ");
	}

	public function down()
	{
		echo "m130118_141056_number_warning_is_required does not support migration down.\n";
		return false;
	}
}