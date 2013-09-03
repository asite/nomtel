<?php

class m130903_193141_add_number_records extends CDbMigration
{
	public function up()
	{
        $trx=Yii::app()->db->beginTransaction();

        $sims=Sim::model()->findAllBySql("
            select s.*
            from sim s
            left outer join number n on (n.sim_id=s.id)
            where s.is_active=1 and s.agent_id is not null and s.id=s.parent_id and s.number is not null and n.number is null;
        ");

        foreach($sims as $sim) {
            $number = new Number;
            $number->sim_id = $sim->id;
            $number->number = $sim->number;
            $number->personal_account = $sim->personal_account;
            $number->status=Number::STATUS_ACTIVE;
            $number->save();

            NumberHistory::addHistoryNumber($number->id,'SIM {Sim:'.$sim->id.'} добавлена в базу','База');
        }

        echo count($sims)." numbers added to database\n";

        $trx->commit();
	}

	public function down()
	{
		echo "m130903_193141_add_number_records does not support migration down.\n";
		return false;
	}
}