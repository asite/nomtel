<?php

class m130213_184603_add_person_fields extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `person`
            ADD `sex` enum('M','F') NOT NULL AFTER `id`,
            CHANGE `passport_issuer` `passport_issuer` varchar(500) COLLATE 'utf8_general_ci' NOT NULL AFTER `passport_issue_date`,
            ADD `passport_issuer_subdivision_code` varchar(200) COLLATE 'utf8_general_ci' NULL AFTER `passport_issuer`,
            CHANGE `birth_place` `birth_place` varchar(500) COLLATE 'utf8_general_ci' NOT NULL AFTER `birth_date`,
            CHANGE `registration_address` `registration_address` varchar(500) COLLATE 'utf8_general_ci' NOT NULL AFTER `birth_place`,
            COMMENT='';
        ");
	}

	public function down()
	{
		echo "m130213_184603_add_person_fields does not support migration down.\n";
		return false;
	}
}