<?php

class m130102_131553_bonus_report_agent_payment_id_can_be_null extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `bonus_report_agent`
            CHANGE `payment_id` `payment_id` int(11) NULL AFTER `sum_referrals`,
            COMMENT='';
        ");
    }

	public function safeDown()
	{
		echo "m130102_131553_bonus_report_agent_payment_id_can_be_null does not support migration down.\n";
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