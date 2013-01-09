<?php

class m130109_075518_change_sum_fields_to_numeric extends CDbMigration
{
	public function safeUp()
	{
        $this->execute("
            DROP TABLE IF EXISTS delivery_report;
        ");

        $this->execute("
            ALTER TABLE `act`
            CHANGE `sum` `sum` decimal(14,2) NOT NULL AFTER `dt`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `agent`
            CHANGE `balance` `balance` decimal(14,2) NOT NULL DEFAULT '0' AFTER `registration_address`,
            CHANGE `stat_acts_sum` `stat_acts_sum` decimal(14,2) NOT NULL DEFAULT '0' AFTER `balance`,
            CHANGE `stat_payments_sum` `stat_payments_sum` decimal(14,2) NOT NULL DEFAULT '0' AFTER `stat_acts_sum`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `balance_report_number`
            CHANGE `balance` `balance` decimal(14,2) NOT NULL AFTER `number_id`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `bonus_report_agent`
            CHANGE `sum` `sum` decimal(14,2) NOT NULL AFTER `sim_count`,
            CHANGE `sum_referrals` `sum_referrals` decimal(14,2) NOT NULL AFTER `sum`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `payment`
            CHANGE `sum` `sum` decimal(14,2) NOT NULL AFTER `dt`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `sim`
            CHANGE `number_price` `number_price` decimal(14,2) NOT NULL DEFAULT '0' AFTER `number`,
            CHANGE `sim_price` `sim_price` decimal(14,2) NOT NULL DEFAULT '0' AFTER `number_price`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `tariff`
            CHANGE `price_agent_sim` `price_agent_sim` decimal(14,2) NOT NULL AFTER `title`,
            CHANGE `price_license_fee` `price_license_fee` decimal(14,2) NOT NULL AFTER `price_agent_sim`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `ticket`
            CHANGE `prise` `prise` decimal(14,2) NULL DEFAULT '0' AFTER `status`,
            COMMENT='';
        ");

	}

	public function safeDown()
	{
		echo "m130109_075518_change_sum_fields_to_numeric does not support migration down.\n";
		return false;
	}
}