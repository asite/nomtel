<?php

class m130116_191020_agreement_fields_can_be_null extends CDbMigration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `subscription_agreement`
            CHANGE `defined_id` `defined_id` varchar(50) COLLATE 'utf8_general_ci' NULL AFTER `id`,
            CHANGE `number_id` `number_id` bigint(20) NULL AFTER `defined_id`,
            CHANGE `person_id` `person_id` bigint(20) NULL AFTER `number_id`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m130116_191020_agreement_fields_can_be_null does not support migration down.\n";
        return false;
    }
}