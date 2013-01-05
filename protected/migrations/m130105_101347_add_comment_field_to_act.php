<?php

class m130105_101347_add_comment_field_to_act extends CDbMigration
{
	public function safeUp()
	{
    $this->execute("ALTER TABLE `act` ADD `comment` mediumtext COLLATE 'utf8_general_ci' NULL, COMMENT=''");
	}

	public function safeDown()
	{
		echo "m130105_101347_add_comment_field_to_act does not support migration down.\n";
		return false;
	}
}