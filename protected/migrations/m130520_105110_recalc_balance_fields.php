<?php

class m130520_105110_recalc_balance_fields extends CDbMigration
{
	public function down()
	{
		echo "m130520_105110_recalc_balance_fields does not support migration down.\n";
		return false;
	}

	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
        $number_ids=$this->dbConnection->createCommand("select distinct number_id from balance_report_number")->queryColumn();
        $count=0;
        foreach($number_ids as $id) {
            $number=Number::model()->findByPk($id);
            // last balance
            $brn=BalanceReportNumber::model()->findBySql("select * from balance_report_number where number_id=:number_id order by id desc limit 0,1",array(':number_id'=>$number->id));
            if ($brn) {
                $number->balance=$brn->balance;

                // find first balance with this value
                $brn2=BalanceReportNumber::model()->findBySql("select * from balance_report_number where number_id=:number_id and balance=:balance order by id asc limit 0,1",
                    array(':number_id'=>$number->id,'balance'=>$brn->balance));

                $number->balance_changed_dt=$brn2->balanceReport->dt;

                $number->save();
            }

            $count++;
            if ($count%100==0) echo "processed $count/".count($number_ids)."\n";
        }
	}
}