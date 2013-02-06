<?php

class m130205_112602_import_icc extends CDbMigration
{
	public function safeUp()
	{
		$file=dirname(__FILE__).'/'.preg_replace('%\.php$%','.txt',basename(__FILE__));
		$f = fopen($file, 'r') or die("Невозможно открыть файл!");
        while (!feof($f)) {
            $text = fgets($f);
            $text = preg_replace('/\t/', " ", $text);
            $text = preg_replace('/\r\n|\r|\n/u', "", $text);
            $text = preg_replace('/(\s){2,}/', "$1", $text);
            $number = explode(" ", $text);

            if ($number[1] && $number[2]) {
            	$criteria = new CDbCriteria();
                $criteria->addCondition('number=:number');
                $criteria->params = array(":number"=>$number[2]);
                Sim::model()->updateAll(array('icc' => $number[1]), $criteria);
            }
        }
	}

	public function safeDown()
	{
		echo "m130205_112602_import_icc does not support migration down.\n";
		return false;
	}
}