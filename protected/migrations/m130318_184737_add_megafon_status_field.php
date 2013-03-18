<?php

class m130318_184737_add_megafon_status_field extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `ticket`
            ADD `megafon_status` enum('DONE','REFUSED') COLLATE 'utf8_general_ci' NULL COMMENT 'для тикетов, которые проходили через мегафон тут хранится ответ оператора' AFTER `status`,
            COMMENT='Хранит тикеты';
        ");
	}

	public function down()
	{
		echo "m130318_184737_add_megafon_status_field does not support migration down.\n";
		return false;
	}
}