<?php

class m130819_141951_change_free_status_to_unknown extends CDbMigration
{
	public function up()
	{
        $this->execute("update number set status='UNKOWN' where status='FREE'");
	}

	public function down()
	{
		echo "m130819_141951_change_free_status_to_unknown does not support migration down.\n";
		return false;
	}
}