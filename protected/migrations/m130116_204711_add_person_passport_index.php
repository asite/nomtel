<?php

class m130116_204711_add_person_passport_index extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `person`
            ADD INDEX `passport_series_passport_number` (`passport_series`, `passport_number`);
        ");
    }

	public function down()
	{
		echo "m130116_204711_add_person_passport_index does not support migration down.\n";
		return false;
	}
}