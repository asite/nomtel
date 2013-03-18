<?php

class m130318_121526_add_table_tmp_number_region extends CDbMigration
{
	public function safeUp()
	{
		$this->execute("
            CREATE TABLE `tmp_number_region` (
			  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			  `start` varchar(50) NOT NULL,
			  `end` varchar(50) NOT NULL,
			  `operator_id` int(11) NOT NULL,
			  `region_id` int(11) NOT NULL,
			  `region` varchar(256) NOT NULL
			) COMMENT='' COLLATE 'utf8_general_ci';
        ");

        $this->execute("
            ALTER TABLE `tmp_number_region`
			ADD INDEX `start` (`start`),
			ADD INDEX `end` (`end`);
        ");
	}

	public function safeDown()
	{
		echo "m130318_121526_add_table_tmp_number_region does not support migration down.\n";
		return false;
	}
}