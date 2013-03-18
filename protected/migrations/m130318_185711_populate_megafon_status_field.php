<?php

class m130318_185711_populate_megafon_status_field extends CDbMigration
{
	public function down()
	{
		echo "m130318_185711_populate_megafon_status_field does not support migration down.\n";
		return false;
	}

	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
        $this->execute("
            update
            ticket set megafon_status='DONE'
            where support_operator_id in (select id from support_operator where role='supportMegafon') and status in ('DONE','FOR_REVIEW');
        ");

        $this->execute("
            update
            ticket set megafon_status='REFUSED'
            where support_operator_id in (select id from support_operator where role='supportMegafon') and status in ('REFUSED','REFUSED_BY_MEGAFON');
        ");
	}
}