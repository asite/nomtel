<?php

class m130808_074926_passport_data_is_not_required_for_agent extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `agent`
            CHANGE `passport_series` `passport_series` varchar(10) COLLATE 'utf8_general_ci' NULL COMMENT 'серия паспорта' AFTER `icq`,
            CHANGE `passport_number` `passport_number` varchar(20) COLLATE 'utf8_general_ci' NULL COMMENT 'номер паспорта' AFTER `passport_series`,
            CHANGE `passport_issue_date` `passport_issue_date` date NULL COMMENT 'дата выдачи паспорта' AFTER `passport_number`,
            CHANGE `passport_issuer` `passport_issuer` varchar(200) COLLATE 'utf8_general_ci' NULL COMMENT 'орган, выдавший паспорт' AFTER `passport_issue_date`,
            CHANGE `birth_date` `birth_date` date NULL COMMENT 'дата рождения' AFTER `passport_issuer`,
            CHANGE `birth_place` `birth_place` varchar(200) COLLATE 'utf8_general_ci' NULL COMMENT 'место рождения' AFTER `birth_date`,
            CHANGE `registration_address` `registration_address` varchar(200) COLLATE 'utf8_general_ci' NULL COMMENT 'адрес регистрации' AFTER `birth_place`,
            COMMENT='Хранит данные агентов. База (админ) имеет запись с id 1';
        ");
	}

	public function down()
	{
		echo "m130808_074926_passport_data_is_not_required_for_agent does not support migration down.\n";
		return false;
	}
}