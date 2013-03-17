<?php

class m130317_225034_recalc_megafon_numbers_balance_statuses extends CDbMigration
{
	public function down()
	{
		echo "m130317_225034_recalc_megafon_numbers_balance_statuses does not support migration down.\n";
		return false;
	}

	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
        $numbers=$this->dbConnection->createCommand("select distinct number from sim where operator_id=2")->queryColumn();
        $processed=0;
        foreach($numbers as $n) {
            $number=Number::model()->findByAttributes(array('number'=>$n));
            if (!$n) {
                echo "can't find number '$n'";
                continue;
            }
            MegafonBalanceEmailImporter::recalcNumberBalanceStatus($number);
            $processed++;
            if ($processed%100==0) {
                echo "processed $processed/".count($numbers)."\n";
            }
        }
	}
}