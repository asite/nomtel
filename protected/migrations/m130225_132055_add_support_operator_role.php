<?php

class m130225_132055_add_support_operator_role extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `support_operator`
            ADD `role` enum('support','supportAdmin','supportMain','supportMegafon') COLLATE 'utf8_general_ci' NOT NULL AFTER `user_id`,
            COMMENT='';
        ");

        $this->execute("update support_operator set role='support'");
	}

	public function down()
	{
		echo "m130225_132055_add_support_operator_role does not support migration down.\n";
		return false;
	}
}