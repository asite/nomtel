<?php

class m130529_063839_import_city_numbers extends CDbMigration
{

	public function readCSV($filename) {
        $csv=array();
        if (($handle = fopen($filename, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 10000)) !== FALSE) {
                $csv[]=$data;
            }
            fclose($handle);
        }
        return $csv;
    }

    public function safeUp()
    {
    	$this->execute("ALTER TABLE `number` ADD `number_city` varchar(50) COLLATE 'utf8_general_ci' NULL COMMENT 'городской номер' AFTER `number`, COMMENT='Хранит данные по номеру';");

        $csv=$this->readCSV(dirname(__FILE__).'/m130529_063839_import_city_numbers.csv');

        foreach($csv as $row) {
            $number=trim($row[0]);
            $city_number=trim($row[1]);

            $this->execute("UPDATE `number` SET `number_city`=CONCAT('".$city_number."',RIGHT(`number`,LENGTH(`number`)-LENGTH('".$number."'))) WHERE `number` LIKE '".$number."%'");
        }
    }

	public function safeDown()
	{
		echo "m130529_063839_import_city_numbers does not support migration down.\n";
		return false;
	}
}