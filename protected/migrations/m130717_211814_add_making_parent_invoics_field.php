<?php

class m130717_211814_add_making_parent_invoics_field extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `agent`
            ADD `is_making_parent_invoices` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Агент может формировать акты передачи симок от своего родителя',
            COMMENT='Хранит данные агентов. База (админ) имеет запись с id 1';
        ");
	}

	public function down()
	{
		echo "m130717_211814_add_making_parent_invoics_field does not support migration down.\n";
		return false;
	}
}