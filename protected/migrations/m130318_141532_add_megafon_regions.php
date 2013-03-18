<?php

class m130318_141532_add_megafon_regions extends CDbMigration
{
	public function safeUp()
	{
		$this->execute("INSERT INTO `operator_region` (`title`, `operator_id`) VALUES ('Нижегородская область', '2');");
		$this->execute("INSERT INTO `operator_region` (`title`, `operator_id`) VALUES ('Белгородская область', '2');");
		$this->execute("INSERT INTO `operator_region` (`title`, `operator_id`) VALUES ('Республика Карелия', '2');");
		$this->execute("INSERT INTO `operator_region` (`title`, `operator_id`) VALUES ('Санкт - Петербург и Ленинградская область', '2');");
		$this->execute("INSERT INTO `operator_region` (`title`, `operator_id`) VALUES ('Ямало - Ненецкий автономный округ |Тюменская область', '2');");
		$this->execute("INSERT INTO `operator_region` (`title`, `operator_id`) VALUES ('Ханты - Мансийский-Югра автономный округ |Тюменская область', '2');");
		$this->execute("INSERT INTO `operator_region` (`title`, `operator_id`) VALUES ('Новокузнецк |Кемеровская область', '2');");
		$this->execute("INSERT INTO `operator_region` (`title`, `operator_id`) VALUES ('Норильск |Красноярский край', '2');");
		$this->execute("INSERT INTO `operator_region` (`title`, `operator_id`) VALUES ('Чукотский автономный округ', '2');");
		$this->execute("INSERT INTO `operator_region` (`title`, `operator_id`) VALUES ('Москва и Московская область', '2');");
		$this->execute("INSERT INTO `operator_region` (`title`, `operator_id`) VALUES ('Тольятти |Самарская область', '2');");
		$this->execute("INSERT INTO `operator_region` (`title`, `operator_id`) VALUES ('Республика Татарстан (Татарстан)', '2');");
		$this->execute("INSERT INTO `operator_region` (`title`, `operator_id`) VALUES ('Набережные Челны |Республика Татарстан (Татарстан)', '2');");
		$this->execute("INSERT INTO `operator_region` (`title`, `operator_id`) VALUES ('Карачаево - Черкесская Республика', '2');");
		$this->execute("INSERT INTO `operator_region` (`title`, `operator_id`) VALUES ('Республика Северная Осетия - Алания', '2');");
		$this->execute("INSERT INTO `operator_region` (`title`, `operator_id`) VALUES ('Кабардино - Балкарская Республика', '2');");
		$this->execute("INSERT INTO `operator_region` (`title`, `operator_id`) VALUES ('Сочи |Краснодарский край', '2');");
		$this->execute("INSERT INTO `operator_region` (`title`, `operator_id`) VALUES ('Минеральные Воды |Ставропольский край', '2');");
	}

	public function safeDown()
	{
		echo "m130318_141532_add_megafon_regions does not support migration down.\n";
		return false;
	}
}