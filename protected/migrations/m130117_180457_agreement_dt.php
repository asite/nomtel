<?php

class m130117_180457_agreement_dt extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `subscription_agreement`
            CHANGE `defined_id` `defined_id` varchar(50) COLLATE 'utf8_general_ci' NOT NULL AFTER `id`,
            ADD `dt` timestamp NOT NULL AFTER `defined_id`,
            COMMENT='';
        ");
        $this->execute("
            ALTER TABLE `subscription_agreement`
            CHANGE `defined_id` `defined_id` varchar(50) COLLATE 'utf8_general_ci' NULL AFTER `id`,
            CHANGE `dt` `dt` timestamp NULL AFTER `defined_id`,
            COMMENT='';
        ");
	}

	public function down()
	{
		echo "m130117_180457_agreement_dt does not support migration down.\n";
		return false;
	}
}