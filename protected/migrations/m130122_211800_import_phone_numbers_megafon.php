<?php

class m130122_211800_import_phone_numbers_megafon extends CDbMigration
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
        return false;

        $csv=$this->readCSV(dirname(__FILE__).'/m130122_211800_import_phone_numbers_megafon.csv');
        array_shift($csv);

        $stat=array(
            'new_numbers'=>0,
            'new_sims'=>0,
            'sims_from_reports'=>0,
            'cnt'=>0
        );
        foreach($csv as $row) {
            $csv_number=trim($row[0]);

            $number=Number::model()->findByAttributes(array('number'=>$csv_number));
            if (!$number) {
                $number=new Number();
                $number->number=$csv_number;
                $stat['new_numbers']++;
            }
            $number->status=Number::STATUS_ACTIVE;

            $sim=$number->sim;
            if (!$sim) {
                $sim=Sim::model()->find(array(
                    'condition'=>'number=:number and parent_agent_id is not null',
                    'params'=>array('number'=>$csv_number)
                ));
                if ($sim) {
                    var_dump($row);
                    echo __LINE__."\n";
                    die;
                }
                // try to use sim that is not currently in base
                $sim=Sim::model()->find(array(
                    'condition'=>'number=:number and parent_agent_id is null',
                    'params'=>array('number'=>$csv_number)
                ));
                if (!$sim) {
                    $sim=new Sim();
                    $sim->save();
                    $sim->parent_id=$sim->id;
                    $sim->operator_id=Operator::OPERATOR_MEGAFON_ID;
                    $stat['new_sims']++;
                } else {
                    $stat['sims_from_reports']++;
                }
                $sim->number=$csv_number;
                $sim->parent_agent_id=1;
                $sim->number_price=0;
                $sim->sim_price=100;
                $sim->save();
                $number->sim_id=$sim->id;
            }
            $number->save();

            $stat['cnt']++;
            if ($stat['cnt']%100==0) {
                echo "processed {$stat['cnt']} of ".count($csv)."\n";
            }
        }
        var_dump($stat);
    }

	public function safeDown()
	{
		echo "m130122_211800_import_phone_numbers_megafon does not support migration down.\n";
		return false;
	}
}