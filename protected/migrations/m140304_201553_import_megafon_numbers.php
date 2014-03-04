<?php

class m140304_201553_import_megafon_numbers extends CDbMigration
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
        array_shift($csv);

        $csv2=$this->readCSV(dirname(__FILE__).'/'.preg_replace('%\.php$%','2.csv',basename(__FILE__)));
        array_shift($csv2);
        array_shift($csv2);
        $numberTariff=[];
        foreach($csv2 as $row)
            $numberTariff[$row[0]]=trim($row[1]);


        $tariffs=array();
        foreach(Tariff::model()->findAllByAttributes(array('operator_id'=>Operator::OPERATOR_MEGAFON_ID)) as $tariff)
            $tariffs[$tariff->title]=$tariff->id;

        $stat=array('cnt'=>0,'skipped_due_empty_number'=>0);
        foreach($csv as $row) {
            $csv_number=trim($row[3]);

            if ($csv_number==='') {
                $stat['skipped_due_empty_number']++;
                continue;
            }

            $csv_personal_account=trim($row[2]);
            $csv_tariff=trim($numberTariff[$csv_number]);

            if ($csv_tariff!='') {
                $tariff=$tariffs[$csv_tariff];
                if (!$tariff) {
                    var_dump($row);
                    var_dump($csv_tariff);
                    echo __LINE__."\n";
                    die;
                }
            }

            $number=new Number();
            $number->number=$csv_number;
            $number->personal_account=$csv_personal_account;
            $number->balance=floatval(str_replace(',','.',$row[5]));
            $number->support_number_region_usage=trim($row[4]);

            $number->status=Number::STATUS_UNKNOWN;

            $sim=new Sim();
            $sim->save();
            $sim->parent_id=$sim->id;
            $sim->personal_account=$csv_personal_account;
            $sim->operator_id=Operator::OPERATOR_MEGAFON_ID;

            if ($csv_tariff!='') $sim->tariff_id=$tariff;
            $sim->company_id=4; // Интрастпей
            $sim->number=$csv_number;
            $sim->parent_agent_id=1;
            $sim->number_price=0;
            $sim->sim_price=100;
            $sim->save();
            $number->sim_id=$sim->id;

            $number->save();

            NumberHistory::addHistoryNumber($number->id,'SIM {Sim:'.$sim->id.'} добавлена в базу','База');

            $stat['cnt']++;
            if ($stat['cnt']%100==0) {
                echo "processed {$stat['cnt']} of ".count($csv)."\n";
            }
        }
        var_dump($stat);
    }

	public function down()
	{
		echo "m140304_201553_import_megafon_numbers does not support migration down.\n";
		return false;
	}
}