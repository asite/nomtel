<?php

class m130815_200049_add_app_restore_fields extends CDbMigration
{
	public function up()
	{
        $this->execute("
ALTER TABLE `megafon_app_restore`
ADD `sent_to_email` tinyint NOT NULL DEFAULT '0' COMMENT 'заявка оправлена на email',
COMMENT='Заявления на восстановление мегафоновских номеров';
        ");

        $this->execute("
ALTER TABLE `megafon_app_restore_number`
ADD `ask_for_sum` tinyint NOT NULL DEFAULT '0' COMMENT 'при входе в восстановление спрашивать сумму' AFTER `cashier_debit_credit_id`,
COMMENT='Мегафоновские номера на восстановление';
        ");
	}

	public function down()
	{
		echo "m130815_200049_add_app_restore_fields does not support migration down.\n";
		return false;
	}
}