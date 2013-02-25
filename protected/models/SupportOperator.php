<?php

Yii::import('application.models._base.BaseSupportOperator');

class SupportOperator extends BaseSupportOperator
{
    const OPERATOR_DEFAULT_ID=4;

    const ROLE_SUPPORT='support';
    const ROLE_SUPPORT_MAIN='supportMain';
    const ROLE_SUPPORT_ADMIN='supportAdmin';
    const ROLE_SUPPORT_MEGAFON='supportMegafon';

    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public static function getComboList($data = array())
    {
        $agents = Yii::app()->db->createCommand("select id,name,surname,middle_name from " .
            self::model()->tableName())->queryAll();

        foreach ($agents as $v) {
            $data[$v['id']] = $v['surname'] . ' ' . $v['name'];
        }

        return $data;
    }
    
    public static function getRoleDropDownList($items=array()) {
        $labels=self::getRoleLabels();
        return array_merge($items,$labels);
    }

    public static function getRoleLabels() {
        static $labels;

        if (!$labels) {
            $labels=array(
                self::ROLE_SUPPORT=>Yii::t('app','support'),
                self::ROLE_SUPPORT_MAIN=>Yii::t('app','supportMain'),
                self::ROLE_SUPPORT_ADMIN=>Yii::t('app','supportAdmin'),
                self::ROLE_SUPPORT_MEGAFON=>Yii::t('app','supportMegafon')
            );
        }

        return $labels;
    }

    public static function getRoleLabel($role) {
        $labels=self::getRoleLabels();
        return $labels[$role];
    }


    public function search() {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('surname', $this->surname, true);
        $criteria->compare('middle_name', $this->middle_name, true);
        $criteria->compare('phone', $this->phone, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('role', $this->role, false);

        $dataProvider=new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));

        $dataProvider->pagination->pageSize=self::ITEMS_PER_PAGE;
        return $dataProvider;
    }

    public function __toString()
    {
        return $this->surname . ' ' . $this->name . ' ' . $this->middle_name;
    }
}