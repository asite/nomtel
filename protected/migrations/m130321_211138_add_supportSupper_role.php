<?php

class m130321_211138_add_supportSupper_role extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `support_operator`
            CHANGE `role` `role` enum('support','supportAdmin','supportMain','supportMegafon','supportSuper') COLLATE 'utf8_general_ci' NOT NULL COMMENT 'роль: support - обычный оператор, supportAdmin - администратор, supportMain - главный оператор, supportMegafon - оператор мегафона ' AFTER `user_id`,
            COMMENT='Хранит операторов техподдержки';
        ");

        $this->execute("update support_operator set role='supportSuper' where id=15");
	}

	public function down()
	{
		echo "m130321_211138_add_supportSupper_role does not support migration down.\n";
		return false;
	}
}