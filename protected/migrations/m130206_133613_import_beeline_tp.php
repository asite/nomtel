<?php

class m130206_133613_import_beeline_tp extends CDbMigration
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

        $agent_id = 27;

        $act = new Act();
        $act->agent_id = $agent_id;
        $act->dt = new EDateTime();
        $act->sum = 0;
        $act->type = Act::TYPE_SIM;
        $act->save();

        $count_simcard = 0;
        $summ_simcard = 0;
        foreach($csv as $row) {
            $csv_number=trim($row[0]);
            if (trim($row[1])) $csv_tariff=trim($row[1]);

            if (!$csv_tariff) {
                var_dump($row);
                echo __LINE__."\n";
                die;
            }

            $sim = Sim::model()->findByAttributes(array('number'=>$csv_number));

            if ($sim->countByAttributes(array('number' => $csv_number))>1) {
                Sim::model()->updateAll(array('tariff_id' => $csv_tariff),'number=:number',array(':number'=>$csv_number));
                NumberHistory::addHistoryNumber($sim->numberObjectBySimId->id,'Тариф сменен с "'.CHtml::encode($sim->tariff).'" на "'. CHtml::encode(Tariff::model()->findByPk($csv_tariff)).'"','База');
                break;
            }

            $old_tariff = $sim->tariff;
            $sim->tariff_id = $csv_tariff;
            $sim->agent_id = $agent_id;
            $sim->act_id = $act->id;
            $sim->save();


            $sql = "INSERT INTO sim (sim_price,personal_account, number,number_price, icc, parent_id, parent_agent_id, parent_act_id, agent_id, act_id, operator_id, tariff_id, operator_region_id, company_id)
                    SELECT s.sim_price, s.personal_account, s.number,s.number_price, s.icc, s.parent_id , ".Yii::app()->db->quoteValue($agent_id).", " . Yii::app()->db->quoteValue($act->id) . ", NULL, NULL, s.operator_id, s.tariff_id, s.operator_region_id, s.company_id
              FROM sim as s
              WHERE number=".$csv_number;

            Yii::app()->db->createCommand($sql)->execute();

            NumberHistory::addHistoryNumber($sim->numberObjectBySimId->id,'Тариф сменен с "'.CHtml::encode($old_tariff).'" на "'. CHtml::encode(Tariff::model()->findByPk($csv_tariff)).'"','База');
            NumberHistory::addHistoryNumber($sim->numberObjectBySimId->id,'SIM передана агенту {Agent:'.$agent_id.'} по акту {Act:'.$act->id.'}','База');
            $count_simcard++;
            $summ_simcard+=$sim->sim_price;
        }
        // update Agent stats
        Agent::deltaSimCount($agent_id, $count_simcard);

        $act->sum = $summ_simcard;
        $act->save();
        $act->agent->recalcBalance();
        $act->agent->save();
	}

	public function safeDown()
	{
		echo "m130206_133613_import_beeline_tp does not support migration down.\n";
		return false;
	}
}