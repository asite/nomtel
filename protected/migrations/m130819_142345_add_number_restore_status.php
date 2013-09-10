<?php

class m130819_142345_add_number_restore_status extends CDbMigration
{
	public function up()
	{
        $this->execute("
ALTER TABLE `number`
CHANGE `status` `status` enum('UNKNOWN','FREE','ACTIVE','BLOCKED','SOLD','RESTORE') COLLATE 'utf8_general_ci' NULL COMMENT 'статус: UNKNOWN- неизвестно, FREE - свободен для подключения, ACTIVE - активен (подключен), BLOCKED - заблокирован (пока не используется), SOLD-продана агенту, RESTORE - находится на восстановлении' AFTER `personal_account`,
COMMENT='Хранит данные по номеру';
        ");
	}

	public function down()
	{
		echo "m130819_142345_add_number_restore_status does not support migration down.\n";
		return false;
	}
}