<?php

Yii::import('application.models._base.BaseSupportOperator');

class SupportOperator extends BaseSupportOperator
{
    const OPERATOR_DEFAULT_ID=4;
    const OPERATOR_ADMIN_ID=15;

    const ROLE_SUPPORT='support';
    const ROLE_SUPPORT_MAIN='supportMain';
    const ROLE_SUPPORT_ADMIN='supportAdmin';
    const ROLE_SUPPORT_MEGAFON='supportMegafon';
    const ROLE_SUPPORT_SUPER='supportSuper';
    const ROLE_SUPPORT_BEELINE='supportBeeline';
    const ROLE_CASHIER='cashier';

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

    public static function getComboListSuper($data = array())
    {
        $agents = Yii::app()->db->createCommand("select id,name,surname,middle_name from " .
            self::model()->tableName()." where role=:role")->queryAll(true,array(':role'=>self::ROLE_SUPPORT_SUPER));

        foreach ($agents as $v) {
            $data[$v['id']] = $v['surname'] . ' ' . $v['name'];
        }

        return $data;
    }

    public static function getCashierComboList($data = array())
    {
        $agents = Yii::app()->db->createCommand("select id,name,surname,middle_name from " .
            self::model()->tableName()." where role='cashier'")->queryAll();

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
                self::ROLE_SUPPORT_MEGAFON=>Yii::t('app','supportMegafon'),
                self::ROLE_SUPPORT_BEELINE=>Yii::t('app','supportBeeline'),
                self::ROLE_SUPPORT_SUPER=>Yii::t('app','supportSuper'),
                self::ROLE_CASHIER=>Yii::t('app','cashier'),
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

    public function getNumbersStats() {
        $data=array(
            Number::SUPPORT_STATUS_UNAVAILABLE=>0,
            Number::SUPPORT_STATUS_CALLBACK=>0,
            Number::SUPPORT_STATUS_REJECT=>0,
            Number::SUPPORT_STATUS_ACTIVE=>0,
            Number::SUPPORT_STATUS_SERVICE_INFO=>0,
            Number::SUPPORT_STATUS_HELP=>0
        );

        $model = Yii::app()->db->createCommand()
            ->select('count(support_status) as count, support_status')
            ->from('number')
            ->where('support_operator_id=:val and support_status is not null and support_passport_need_validation=0', array(':val'=>$this->id))
            ->group('support_status')
            ->queryAll();

        foreach ($model as $v) {
            $data[$v['support_status']]=$v['count'];
        }

        return $data;
    }

    public function getTicketsStats() {
        $stats=array();

        $criteria=new CDbCriteria();
        if (Yii::app()->user->role=='supportMegafon') {
            $criteria->AddInCondition('status',array(Ticket::STATUS_IN_WORK_MEGAFON));
        } else {
            $criteria->compare('support_operator_id',$this->id);
            $criteria->AddInCondition('status',array(Ticket::STATUS_IN_WORK_OPERATOR));
        }

        $stats['Обращений в работе у оператора']=Ticket::model()->count($criteria);

        $criteria=new CDbCriteria();
        $criteria->compare('support_operator_id',$this->id);
        if (Yii::app()->user->role=='supportMegafon') {
            $criteria->AddNotInCondition('status',array(Ticket::STATUS_IN_WORK_MEGAFON));
        } else {
            $criteria->AddNotInCondition('status',array(Ticket::STATUS_IN_WORK_OPERATOR));
        }

        $stats['Обращений обработано оператором']=Ticket::model()->count($criteria);

        $criteria->compare('type',Ticket::TYPE_OCR_DOCS);
        $stats['Из них на "оцифровку"']=Ticket::model()->count($criteria);

        return $stats;
    }

    public function __toString()
    {
        return $this->surname . ' ' . $this->name . ' ' . $this->middle_name;
    }
}