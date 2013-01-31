<?php

class m130131_215206_import_megafon extends CDbMigration
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
            $tariffs[$tariff->title]=$tariff->id;

        $stat=array('cnt'=>0);
        foreach($csv as $row) {
            $csv_personal_account=trim($row[0]);
            $csv_number=trim($row[1]);
            $csv_service_password=trim($row[2]);
            $csv_code_word=trim($row[3]);
            $csv_tariff=trim($row[4]);
            $csv_status=trim($row[5]);

            if ($csv_tariff!='') {
                $tariff=$tariffs[$csv_tariff];
                if (!$tariff) {
                    var_dump($row);
                    echo __LINE__."\n";
                    die;
                }
            }

            $number=new Number();
            $number->number=$csv_number;
            $number->personal_account=$csv_personal_account;

            switch ($csv_status) {
                case 'Свободный':
                    $number->status=Number::STATUS_FREE;
                    break;
                default:
                    var_dump($row);
                    echo __LINE__."\n";
                    die;
            }

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
            $number->codeword=$csv_code_word;
            $number->service_password=$csv_service_password;
            $number->sim_id=$sim->id;

            $number->save();

            NumberHistory::addHistoryNumber($number->id,'SIM {Sim:'.$sim->id.'} добавлена в базу','База');

            $stat['cnt']++;
            if ($stat['cnt']%1000==0) {
                echo "processed {$stat['cnt']} of ".count($csv)."\n";
            }
        }
        var_dump($stat);
    }

	public function down()
	{
		echo "m130131_215206_import_megafon does not support migration down.\n";
		return false;
	}
}