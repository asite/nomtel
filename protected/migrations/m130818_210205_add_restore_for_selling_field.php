<?php

class m130818_210205_add_restore_for_selling_field extends CDbMigration
{
	public function up()
	{
        $this->execute("
ALTER TABLE `megafon_app_restore_number`
ADD `restore_for_selling` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'восстановление на продажу',
COMMENT='Мегафоновские номера на восстановление';
        ");
	}

	public function down()
	{
		echo "m130818_210205_add_restore_for_selling_field does not support migration down.\n";
		return false;
	}
}