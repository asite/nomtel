<?php

/**
 * This is the model base class for the table "agent".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Agent".
 *
 * Columns in table "agent" available as properties of the model,
 * followed by relations of table "agent" available as properties of the model.
 *
 * @property string $id
 * @property string $parent_id
 * @property string $user_id
 * @property string $name
 * @property string $surname
 * @property string $middle_name
 * @property string $phone_1
 * @property string $phone_2
 * @property string $phone_3
 * @property string $email
 * @property string $skype
 * @property string $icq
 * @property string $passport_series
 * @property string $passport_number
 * @property string $passport_issue_date
 * @property string $passport_issuer
 * @property string $birthday_date
 * @property string $birthday_place
 * @property string $registration_address
 * @property double $balance
 *
 * @property User $parent
 * @property User $user
 * @property DeliveryReport[] $deliveryReports
 * @property Payment[] $payments
 * @property Sim[] $sims
 * @property Sim[] $sims1
 */
abstract class BaseAgent extends BaseGxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'agent';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'Agent|Agents', $n);
	}

	public static function representingColumn() {
		return 'name';
	}

	public function rules() {
		return array(
			array('name, surname, middle_name, phone_1, passport_series, passport_number, passport_issue_date, passport_issuer, birthday_date, birthday_place, registration_address', 'required'),
			array('balance', 'numerical'),
			array('parent_id, user_id, icq, passport_number', 'length', 'max'=>20),
			array('name, surname, middle_name, email, skype', 'length', 'max'=>100),
			array('phone_1, phone_2, phone_3', 'length', 'max'=>50),
			array('passport_series', 'length', 'max'=>10),
			array('passport_issuer, birthday_place, registration_address', 'length', 'max'=>200),
			array('parent_id, user_id, phone_2, phone_3, email, skype, icq, balance', 'default', 'setOnEmpty' => true, 'value' => null),
            array('passport_issue_date','date','format'=>'dd.MM.yyyy'),
            array('birthday_date','date','format'=>'dd.MM.yyyy'),
			array('id, parent_id, user_id, name, surname, middle_name, phone_1, phone_2, phone_3, email, skype, icq, passport_series, passport_number, passport_issue_date, passport_issuer, birthday_date, birthday_place, registration_address, balance', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'parent' => array(self::BELONGS_TO, 'User', 'parent_id'),
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
			'deliveryReports' => array(self::HAS_MANY, 'DeliveryReport', 'agent_id'),
			'payments' => array(self::HAS_MANY, 'Payment', 'agent_id'),
			'sims' => array(self::HAS_MANY, 'Sim', 'parent_agent_id'),
			'sims1' => array(self::HAS_MANY, 'Sim', 'agent_id'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'parent_id' => null,
			'user_id' => null,
			'name' => Yii::t('app', 'Name'),
			'surname' => Yii::t('app', 'Surname'),
			'middle_name' => Yii::t('app', 'Middle Name'),
			'phone_1' => Yii::t('app', 'Phone 1'),
			'phone_2' => Yii::t('app', 'Phone 2'),
			'phone_3' => Yii::t('app', 'Phone 3'),
			'email' => Yii::t('app', 'Email'),
			'skype' => Yii::t('app', 'Skype'),
			'icq' => Yii::t('app', 'Icq'),
			'passport_series' => Yii::t('app', 'Passport Series'),
			'passport_number' => Yii::t('app', 'Passport Number'),
			'passport_issue_date' => Yii::t('app', 'Passport Issue Date'),
			'passport_issuer' => Yii::t('app', 'Passport Issuer'),
			'birthday_date' => Yii::t('app', 'Birthday Date'),
			'birthday_place' => Yii::t('app', 'Birthday Place'),
			'registration_address' => Yii::t('app', 'Registration Address'),
			'balance' => Yii::t('app', 'Balance'),
			'parent' => null,
			'user' => null,
			'deliveryReports' => null,
			'payments' => null,
			'sims' => null,
			'sims1' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('parent_id', $this->parent_id);
		$criteria->compare('user_id', $this->user_id);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('surname', $this->surname, true);
		$criteria->compare('middle_name', $this->middle_name, true);
		$criteria->compare('phone_1', $this->phone_1, true);
		$criteria->compare('phone_2', $this->phone_2, true);
		$criteria->compare('phone_3', $this->phone_3, true);
		$criteria->compare('email', $this->email, true);
		$criteria->compare('skype', $this->skype, true);
		$criteria->compare('icq', $this->icq, true);
		$criteria->compare('passport_series', $this->passport_series, true);
		$criteria->compare('passport_number', $this->passport_number, true);
		$criteria->compare('passport_issue_date', $this->passport_issue_date, true);
		$criteria->compare('passport_issuer', $this->passport_issuer, true);
		$criteria->compare('birthday_date', $this->birthday_date, true);
		$criteria->compare('birthday_place', $this->birthday_place, true);
		$criteria->compare('registration_address', $this->registration_address, true);
		$criteria->compare('balance', $this->balance);

		$dataProvider=new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));

        $dataProvider->pagination->pageSize=self::ITEMS_PER_PAGE;
        return $dataProvider;
	}

    public function convertDateTimeFieldsToEDateTime() {
        // rest of work will do setAttribute() routine
        $this->setAttribute('passport_issue_date',strval($this->passport_issue_date));
        $this->setAttribute('birthday_date',strval($this->birthday_date));
    }

    public function convertDateTimeFieldsToString() {
        if (is_object($this->passport_issue_date) && get_class($this->passport_issue_date)=='EDateTime') $this->passport_issue_date=new EString($this->passport_issue_date->format(self::$mySqlDateFormat));
        if (is_object($this->birthday_date) && get_class($this->birthday_date)=='EDateTime') $this->birthday_date=new EString($this->birthday_date->format(self::$mySqlDateFormat));
    }

    public function afterFind() {
        $this->convertDateTimeFieldsToEDateTime();
    }

    private function convertStringToEDateTime($val,$type) {
        if (!$val) return null;
        try {
            $val=new EDateTime($val,null,$type);
        } catch (Exception $e) {
        }
        return $val;
    }

    public function setAttribute($name,$value) {
        if (is_string($value)) {
            if ($name=='passport_issue_date') $value=$this->convertStringToEDateTime($value,'date');
            if ($name=='birthday_date') $value=$this->convertStringToEDateTime($value,'date');
        }
        return parent::setAttribute($name,$value);
    }

    public function beforeSave() {
        $this->convertDateTimeFieldsToString();

        return true;
    }

    public function afterSave() {
        $this->convertDateTimeFieldsToEDateTime();
    }
}