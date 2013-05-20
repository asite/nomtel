<?php

class m130429_064507_add_sold_status extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `number`
            CHANGE `status` `status` enum('UNKNOWN','FREE','ACTIVE','BLOCKED','SOLD') COLLATE 'utf8_general_ci' NULL COMMENT 'статус: UNKNOWN- неизвестно, FREE - свободен для подключения, ACTIVE - активен (подключен), BLOCKED - заблокирован (пока не используется), SOLD-продана агенту' AFTER `personal_account`,
            COMMENT='Хранит данные по номеру';
        ");
	}

	public function down()
	{
		echo "m130429_064507_add_sold_status does not support migration down.\n";
		return false;
	}
}