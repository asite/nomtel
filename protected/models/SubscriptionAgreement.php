<?php

Yii::import('application.models._base.BaseSubscriptionAgreement');

class SubscriptionAgreement extends BaseSubscriptionAgreement
{
    public function fillDefinedId() {
        $this->defined_id=$this->id.$this->dt->format('dmy');
    }

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function rules() {
        $rules=parent::rules();

        $this->addRules($rules,array(
            array('person_id, number_id','required','on'=>'create')
        ));

        return $rules;
    }

    public function __toString() {
        return $this->defined_id;
    }
}