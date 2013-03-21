<?php

class m130321_131043_add_sim_prefix_region_table extends CDbMigration
{
	public function up()
	{
        $this->execute("
            CREATE TABLE `icc_prefix_region` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
          `icc_prefix` varchar(200) NOT NULL,
          `operator_id` int(11) NOT NULL,
          `operator_region_id` int(11) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `operator_id` (`operator_id`),
          KEY `operator_region_id` (`operator_region_id`),
          CONSTRAINT `icc_prefix_region_ibfk_1` FOREIGN KEY (`operator_id`) REFERENCES `operator` (`id`),
          CONSTRAINT `icc_prefix_region_ibfk_2` FOREIGN KEY (`operator_region_id`) REFERENCES `operator_region` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        $this->execute("
            INSERT INTO `icc_prefix_region` (`id`, `icc_prefix`, `operator_id`, `operator_region_id`) VALUES
            (1,	'89701020223',	2,	169),
            (2,	'89701020224',	2,	129),
            (3,	'89701020226',	2,	170);
        ");
	}

	public function down()
	{
		echo "m130321_131043_add_sim_prefix_region_table does not support migration down.\n";
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