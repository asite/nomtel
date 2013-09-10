<?php

class m130807_052823_add_require_password_change_field extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `agent`
            ADD `require_password_change` tinyint(1) NOT NULL DEFAULT '0',
            COMMENT='Хранит данные агентов. База (админ) имеет запись с id 1';
        ");
	}

	public function down()
	{
		echo "m130807_052823_add_require_password_change_field does not support migration down.\n";
		return false;
	}
}