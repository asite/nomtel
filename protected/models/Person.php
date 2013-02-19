<?php

Yii::import('application.models._base.BasePerson');

class Person extends BasePerson
{
    const SEX_MALE='M';
    const SEX_FEMALE='F';

    public function __toString()
    {
        return $this->surname . ' ' . $this->name . ' ' . $this->middle_name;
    }

    public static function getSexLabels() {
        static $labels;

        if (!$labels) {
            $labels=array(
                self::SEX_MALE=>'Муж',
                self::SEX_FEMALE=>'Жен',
            );
        }

        return $labels;
    }

    public function getSexLabel($sex) {
        $labels=self::getSexLabels();
        return $labels[$sex];
    }

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

}