<?php

Yii::import('application.models._base.BaseAgent');

class Agent extends BaseAgent
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

/*
    public function convertDateTimeFieldsToEDateTime() {
        // rest of work will do setAttribute() routine
        $this->setAttribute('birthday_date',strval($this->birthday_date));
        $this->setAttribute('passport_issue_date',strval($this->birthday_date));
    }

    public function convertDateTimeFieldsToString() {
        if (is_object($this->birthday_date) && get_class($this->birthday_date)=='EDateTime') $this->birthday_date=new EString($this->birthday_date->format(self::$mySqlDateFormat));
        if (is_object($this->passport_issue_date) && get_class($this->passport_issue_date)=='EDateTime') $this->passport_issue_date=new EString($this->passport_issue_date->format(self::$mySqlDateFormat));
    }

    public function afterFind() {
        $this->convertDateTimeFieldsToEDateTime();
    }

    public function setAttribute($column,$val) {
        if (is_string($val)) {
            if ($column=='birthday_date') $val=$val ? new EDateTime($val,null,'date'):null;
            if ($column=='passport_issue_date') $val=$val ? new EDateTime($val,null,'date'):null;
        }
        parent::setAttribute($column,$val);
    }

    public function beforeSave()
    {
        $this->convertDateTimeFieldsToString();

        return true;
    }

    public function afterSave() {
        $this->convertDateTimeFieldsToEDateTime();
    }
*/
}