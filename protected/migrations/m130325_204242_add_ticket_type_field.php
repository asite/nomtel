<?php

class m130325_204242_add_ticket_type_field extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `ticket`
            ADD `type` enum('NORMAL','OCR_DOCS') NOT NULL DEFAULT 'NORMAL' COMMENT 'тип тикета NORMAL- обычный, OCR_DOCS - заполнение данных абонента по сканам' AFTER `number_id`,
            COMMENT='Хранит тикеты';
        ");
	}

	public function down()
	{
		echo "m130325_204242_add_ticket_type_field does not support migration down.\n";
		return false;
	}
}