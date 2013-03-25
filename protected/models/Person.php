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

    public function rules() {
        return array_merge(parent::rules(),array(
            array('sex,name,surname,middle_name,passport_series,passport_number,passport_issue_date,passport_issuer,birth_date,birth_place,registration_address','required','on'=>'activating')
        ));
    }
}