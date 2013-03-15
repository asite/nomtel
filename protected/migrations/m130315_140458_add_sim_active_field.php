<?php

class m130315_140458_add_sim_active_field extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `sim`
            ADD `is_active` tinyint NULL DEFAULT '1' COMMENT '0 - ����� �� ������� (���� ������������� �� ������)',
            COMMENT='������ ���������� � ������ � �� �������� ����� ��������. ���� ����� ������ ����� N ������� (������� ����), �� � ������� ����� N ������� ��� ���� �����';
        ");
    }

	public function down()
	{
		echo "m130315_140458_add_sim_active_field does not support migration down.\n";
		return false;
	}
}