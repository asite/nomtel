<?php

class m130819_120442_add_foreign_keys extends CDbMigration
{
	public function up()
	{
	    $this->execute("
ALTER TABLE `cashier_sell_number`
ADD FOREIGN KEY (`support_operator_id`) REFERENCES `support_operator` (`id`),
ADD FOREIGN KEY (`number_id`) REFERENCES `number` (`id`),
COMMENT='Номера, проданные/восстановленные кассиром';
	    ");
	}

	public function down()
	{
		echo "m130819_120442_add_foreign_keys does not support migration down.\n";
		return false;
	}
}