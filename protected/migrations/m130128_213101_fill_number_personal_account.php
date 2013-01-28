<?php

class m130128_213101_fill_number_personal_account extends CDbMigration
{
	public function safeUp()
	{
        $this->db->execute("
          update number,sim set number.personal_account=sim.personal_account where number.personal_account is null and number.sim_id=sim.id;
        ");
	}

	public function safeDown()
	{
		echo "m130128_213101_fill_number_personal_account does not support migration down.\n";
		return false;
	}
}