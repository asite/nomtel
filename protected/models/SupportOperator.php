<?php

Yii::import('application.models._base.BaseSupportOperator');

class SupportOperator extends BaseSupportOperator
{
    const OPERATOR_DEFAULT_ID=4;

    public static function getComboList($data = array())
    {
        $agents = Yii::app()->db->createCommand("select id,name,surname,middle_name from " .
            self::model()->tableName())->queryAll();

        foreach ($agents as $v) {
            $data[$v['id']] = $v['surname'] . ' ' . $v['name'];
        }

        return $data;
    }


    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function __toString()
    {
        return $this->surname . ' ' . $this->name . ' ' . $this->middle_name;
    }
}