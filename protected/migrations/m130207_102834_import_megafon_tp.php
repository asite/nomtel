<?php

class m130207_102834_import_megafon_tp extends CDbMigration
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
		$csv=$this->readCSV(dirname(__FILE__).'/'.preg_replace('%\.php$%','.csv',basename(__FILE__)));
        array_shift($csv);

        $tariffs=array();
        foreach(Tariff::model()->findAllByAttributes(array('operator_id'=>Operator::OPERATOR_MEGAFON_ID)) as $tariff)
            $tariffs[mb_strtolower($tariff->title,'UTF-8')]=$tariff->id;

        $mapTariff = array(
        	'территория' => 'территория 1500',
        	'фирменный универсальный' => 'фирменный универсальный',
        	'альянс 1' => 'альянс 1',
        	'альянс 3' => 'альянс 3 пакет максимальный',
        	'мегафон-логин' => 'мегафон-логин',
        	'безлимитный тюмень (pstn)'=>'безлимитный тюмень (pstn)',
        	'сотрудник' => 'сотрудник',
        	);

        $stats['all'] = 0;
        $stats['changed'] = 0;
        $stats['new'] = 0;
        foreach($csv as $row) {
            $csv_number=trim($row[0]);
            $csv_tariff=mb_strtolower(trim($row[1]),'UTF-8');

            if ($csv_tariff!='') {
                $tariff=$tariffs[$mapTariff[$csv_tariff]];
                if (!$tariff) {
                    var_dump($row);
                    echo __LINE__."\n";
                    die;
                }
            }

            $sim = Sim::model()->findByAttributes(array('number'=>$csv_number),array('order'=>'id DESC'));
            if (!empty($sim)) {
            	$stats['changed']++;
              	Sim::model()->updateAll(array('tariff_id' => $tariff),'number=:number',array(':number'=>$csv_number));
              	if (isset($sim->tariff))
                	NumberHistory::addHistoryNumber($sim->numberObjectBySimId->id,'Тариф сменен с "'.CHtml::encode($sim->tariff).'" на "'. CHtml::encode(Tariff::model()->findByPk($tariff)).'"','База');
              	else {
                	NumberHistory::addHistoryNumber($sim->numberObjectBySimId->id,'Установлен тариф "'.CHtml::encode(Tariff::model()->findByPk($tariff)).'"','База');
              	}
        	} else {
        		$stats['new']++;
        		$model = new Sim;
                $model->number = $csv_number;
                $model->operator_id = Operator::OPERATOR_MEGAFON_ID;
                $model->parent_agent_id = 1;
                $model->tariff_id = $tariff;
                $model->save();
                $model->parent_id = $model->id;
                $model->save();


	            $number = new Number;
	            $number->sim_id = $model->id;
	            $number->number = $csv_number;
	            $number->status = Number::STATUS_FREE;
	            $number->save();
	            NumberHistory::addHistoryNumber($number->id,'SIM {Sim:'.$model->id.'} добавлена в базу','База');
        	}
            $stats['all']++;
        }
        print_r($stats);
	}

	public function safeDown()
	{
		echo "m130207_102834_import_megafon_tp does not support migration down.\n";
		return false;
	}
}