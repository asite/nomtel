<?php

class m130210_122307_add_support_operator_email extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `support_operator`
            ADD `email` varchar(200) COLLATE 'utf8_general_ci' NOT NULL,
            COMMENT='';
        ");
	}

	public function down()
	{
		echo "m130210_122307_add_support_operator_email does not support migration down.\n";
		return false;
	}
}