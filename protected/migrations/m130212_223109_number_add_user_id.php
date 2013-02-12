<?php

class m130212_223109_number_add_user_id extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `number`
            ADD `user_id` int(11) NULL,
            ADD FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
            COMMENT='';
        ");
	}

	public function down()
	{
		echo "m130212_223109_number_add_user_id does not support migration down.\n";
		return false;
	}
}