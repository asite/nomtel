<?php

class m130730_065301_recalc_balance_status_changed_dt extends CDbMigration
{
	public function down()
	{
		echo "m130730_065301_recalc_balance_status_changed_dt does not support migration down.\n";
		return false;
	}

	// Use safeUp/safeDown to do migration with transaction
	public function up()
	{
        $this->execute("update number set balance_status_changed_dt=NOW()");

        $trx=$this->dbConnection->beginTransaction();

        $ids=$this->dbConnection->createCommand("select id from number where balance_status='CLOSED' and id in (select distinct number_id from balance_report_number)")->queryColumn();
        $i=0;
        $currentReportId=$this->dbConnection->createCommand("select max(id) from balance_report")->queryScalar();
        foreach($ids as $id) {
            $number=Number::model()->findByPk($id);
            $maxReportId=$this->dbConnection->createCommand("select max(balance_report_id) from balance_report_number where number_id=:number")->queryScalar(array(':number'=>$id));
            $balance_changed_dt=new EDateTime;
            $balance_changed_dt->modify('- '.($currentReportId-$maxReportId-10).' DAY');
            $number->balance_status_changed_dt=$balance_changed_dt;
            $number->save();
            $i++;
            if ($i%100==0) echo $i.'/'.count($ids)." CLOSED statuses processed\n";
        }

        $ids=$this->dbConnection->createCommand("select id from number where balance_status='NOT_CHANGING'")->queryColumn();
        $i=0;
        foreach($ids as $id) {
            $number=Number::model()->findByPk($id);
            $balances=$this->dbConnection->createCommand("select balance from balance_report_number where number_id=:number_id order by balance_report_id desc limit 1,1000")->queryColumn(array(':number_id'=>$id));
            //echo "processing $id\n ".implode(',',$balances)."\n";
            $eqBalances=1;
            $maxIdx=count($balances)-1;
            while(abs($balances[0]-$balances[$eqBalances])<1e-6 && $eqBalances<$maxIdx) {
                $eqBalances++;
            }

            $balance_changed_dt=new EDateTime;
            $balance_changed_dt->modify('- '.($eqBalances-6).' DAY');
            $number->balance_status_changed_dt=$balance_changed_dt;

            $number->save();
            $i++;
            if ($i%100==0) echo $i.'/'.count($ids)." NOT_CHANGING statuses processed\n";
        }

        $trx->commit();
	}

}