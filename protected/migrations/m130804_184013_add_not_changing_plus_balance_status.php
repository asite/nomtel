<?php

class m130804_184013_add_not_changing_plus_balance_status extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `number`
            CHANGE `balance_status` `balance_status` enum('CHANGING','NOT_CHANGING','NOT_CHANGING_PLUS','NO_DATA','CLOSED') COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'NO_DATA' COMMENT 'статус баланса' AFTER `status`,
            COMMENT='Хранит данные по номеру';
        ");

        $trx=$this->dbConnection->beginTransaction();

        $number_ids=$this->dbConnection->createCommand("select distinct number_id from balance_report_number")->queryColumn();
        $i=0;
        foreach($number_ids as $number_id) {
            $number=Number::model()->findByPk($number_id);
            Number::recalcNumberBalance($number);
            $number->save();
            $i++;
            if ($i%100==0) echo "$i/".count($number_ids)." processed\n";
        }

        $trx->commit();
	}

	public function down()
	{
		echo "m130804_184013_add_not_changing_plus_balance_status does not support migration down.\n";
		return false;
	}
}