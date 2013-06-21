<?php

class m130621_073522_add extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `support_operator`
            CHANGE `role` `role` enum('support','supportAdmin','supportMegafon','supportSuper','supportBeeline','cashier') COLLATE 'utf8_general_ci' NOT NULL COMMENT 'роль: support - обычный оператор, supportAdmin - администратор, supportMain - главный оператор, supportMegafon - оператор мегафона, supportSuper - помощник админа, cashier - кассир' AFTER `user_id`,
            COMMENT='Хранит операторов техподдержки';
        ");
	}

	public function down()
	{
		echo "m130621_073522_add does not support migration down.\n";
		return false;
	}
}