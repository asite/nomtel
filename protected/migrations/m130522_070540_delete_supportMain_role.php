<?php

class m130522_070540_delete_supportMain_role extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `support_operator`
            CHANGE `role` `role` enum('support','supportAdmin','supportMegafon','supportSuper','cashier') COLLATE 'utf8_general_ci' NOT NULL COMMENT 'роль: support - обычный оператор, supportAdmin - администратор, supportMain - главный оператор, supportMegafon - оператор мегафона, supportSuper - помощник админа, cashier - кассир' AFTER `user_id`,
            COMMENT='Хранит операторов техподдержки';
        ");

        $this->execute("
            update ticket set status='REFUSED' where status in ('REFUSED_BY_MEGAFON','REFUSED_BY_ADMIN','REFUSED_BY_OPERATOR')
        ");

        $this->execute("
            update ticket set status='DONE' where status='FOR_REVIEW'
        ");

        $this->execute("
            ALTER TABLE `ticket`
            CHANGE `status` `status` enum('NEW','IN_WORK_MEGAFON','IN_WORK_OPERATOR','DONE','REFUSED') COLLATE 'utf8_general_ci' NOT NULL COMMENT 'статус тикета' AFTER `dt`,
            COMMENT='Хранит тикеты';
        ");
	}

	public function down()
	{
		echo "m130522_070540_delete_supportMain_role does not support migration down.\n";
		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}