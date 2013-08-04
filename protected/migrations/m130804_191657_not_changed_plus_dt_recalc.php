<?php

class m130804_191657_not_changed_plus_dt_recalc extends CDbMigration
{
	public function up()
	{
        $trx=$this->dbConnection->beginTransaction();

        $ids=$this->dbConnection->createCommand("select id from number where balance_status='NOT_CHANGING_PLUS'")->queryColumn();
        $i=0;
        foreach($ids as $id) {
            $number=Number::model()->findByPk($id);
            $balances=$this->dbConnection->createCommand("select balance from balance_report_number where number_id=:number_id order by balance_report_id desc limit 1,1000")->queryColumn(array(':number_id'=>$id));
            //echo "processing $id\n ".implode(',',$balances)."\n";
            $eqBalances=1;
            $maxIdx=count($balances)-1;
            if (!empty($balances));
            while(abs($balances[0]-$balances[$eqBalances])<1e-6 && $eqBalances<$maxIdx) {
                $eqBalances++;
            }

            $balance_changed_dt=new EDateTime;
            if ($eqBalances>=6) $balance_changed_dt->modify('- '.($eqBalances-6).' DAY');
            $number->balance_status_changed_dt=$balance_changed_dt;

            $number->save();
            $i++;
            if ($i%100==0) echo $i.'/'.count($ids)." NOT_CHANGING statuses processed\n";
        }

        $trx->commit();
	}

	public function down()
	{
		echo "m130804_191657_not_changed_plus_dt_recalc does not support migration down.\n";
		return false;
	}
}