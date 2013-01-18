<?php

class m130118_130818_populate_number_and_number_history extends CDbMigration
{
	public function safeUp()
	{
        $this->execute('delete from subscription_agreement_file');
        $this->execute('delete from subscription_agreement');
        $this->execute('delete from person_file');
        $this->execute('delete from person');
        $this->execute('delete from file');
        $this->execute('delete from number_history');
        $this->execute('delete from number');

        $numbers=$this->dbConnection->createCommand("select distinct number,personal_account,id from sim where parent_agent_id=1")->queryAll();
        foreach($numbers as $number) {
            $m=new Number();
            $m->status=Number::STATUS_FREE;
            $m->number=$number['number'];
            $m->personal_account=$number['personal_account'];
            $m->sim_id=$number['id'];
            $m->save();
            NumberHistory::addHistoryNumber($m->id,'SIM {Sim:'.$number['id'].'} добавлена в базу','База');
        }
	}

	public function safeDown()
	{
        echo "m130118_130818_populate_number_and_number_history does not support migration down.\n";
        return false;
	}
}