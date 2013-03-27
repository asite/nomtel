<?php

class m130325_192000_person_fields_can_be_null extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `person`
            CHANGE `sex` `sex` enum('M','F') COLLATE 'utf8_general_ci' NULL COMMENT 'пол' AFTER `id`,
            CHANGE `name` `name` varchar(200) COLLATE 'utf8_general_ci' NULL COMMENT 'имя' AFTER `sex`,
            CHANGE `surname` `surname` varchar(200) COLLATE 'utf8_general_ci' NULL COMMENT 'фамилия' AFTER `name`,
            CHANGE `middle_name` `middle_name` varchar(200) COLLATE 'utf8_general_ci' NULL COMMENT 'отчество' AFTER `surname`,
            CHANGE `passport_series` `passport_series` varchar(10) COLLATE 'utf8_general_ci' NULL COMMENT 'серия паспорта' AFTER `email`,
            CHANGE `passport_number` `passport_number` varchar(20) COLLATE 'utf8_general_ci' NULL COMMENT 'номер паспорта' AFTER `passport_series`,
            CHANGE `passport_issue_date` `passport_issue_date` date NULL COMMENT 'дата выдачи паспорта' AFTER `passport_number`,
            CHANGE `passport_issuer` `passport_issuer` varchar(500) COLLATE 'utf8_general_ci' NULL COMMENT 'орган, выдавший паспорт' AFTER `passport_issue_date`,
            CHANGE `birth_date` `birth_date` date NULL COMMENT 'дата рождения' AFTER `passport_issuer_subdivision_code`,
            CHANGE `birth_place` `birth_place` varchar(500) COLLATE 'utf8_general_ci' NULL COMMENT 'место рождения' AFTER `birth_date`,
            CHANGE `registration_address` `registration_address` varchar(500) COLLATE 'utf8_general_ci' NULL COMMENT 'адрес регистрации' AFTER `birth_place`,
            COMMENT='хранит персональные данные абонентов';
        ");
	}

	public function down()
	{
		echo "m130325_192000_person_fields_can_be_null does not support migration down.\n";
		return false;
	}
}