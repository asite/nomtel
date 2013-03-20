<?php

/**
 * This is the model base class for the table "number".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Number".
 *
 * Columns in table "number" available as properties of the model,
 * followed by relations of table "number" available as properties of the model.
 *
 * @property string $id
 * @property string $sim_id
 * @property string $number
 * @property string $personal_account
 * @property string $status
 * @property string $balance_status
 * @property string $balance_status_changed_dt
 * @property string $codeword
 * @property string $service_password
 * @property string $short_number
 * @property integer $support_operator_id
 * @property string $support_operator_got_dt
 * @property string $support_dt
 * @property string $support_status
 * @property string $support_callback_dt
 * @property string $support_callback_name
 * @property string $support_getting_passport_variant
 * @property string $support_number_region_usage
 * @property string $support_sent_sms_status
 * @property integer $user_id
 * @property integer $support_passport_need_validation
 *
 * @property BalanceReportNumber[] $balanceReportNumbers
 * @property BonusReportNumber[] $bonusReportNumbers
 * @property Sim $sim
 * @property SupportOperator $supportOperator
 * @property User $user
 * @property NumberHistory[] $numberHistories
 * @property SubscriptionAgreement[] $subscriptionAgreements
 * @property Ticket[] $tickets
 */
abstract class BaseNumber extends BaseGxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'number';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'Number|Numbers', $n);
	}

	public static function representingColumn() {
		return 'number';
	}

	public function rules() {
		return array(
			array('number', 'required'),
			array('support_operator_id, user_id, support_passport_need_validation', 'numerical', 'integerOnly'=>true),
			array('sim_id, codeword, service_password, short_number', 'length', 'max'=>20),
			array('number, personal_account', 'length', 'max'=>50),
			array('status', 'length', 'max'=>7),
			array('balance_status', 'length', 'max'=>16),
			array('support_status', 'length', 'max'=>12),
			array('support_callback_name, support_getting_passport_variant, support_number_region_usage', 'length', 'max'=>200),
			array('support_sent_sms_status', 'length', 'max'=>6),
			array('balance_status_changed_dt, support_operator_got_dt, support_dt, support_callback_dt', 'safe'),
			array('sim_id, personal_account, status, balance_status, balance_status_changed_dt, codeword, service_password, short_number, support_operator_id, support_operator_got_dt, support_dt, support_status, support_callback_dt, support_callback_name, support_getting_passport_variant, support_number_region_usage, support_sent_sms_status, user_id, support_passport_need_validation', 'default', 'setOnEmpty' => true, 'value' => null),
            array('balance_status_changed_dt','date','format'=>'dd.MM.yyyy HH:mm:ss'),
            array('support_operator_got_dt','date','format'=>'dd.MM.yyyy HH:mm:ss'),
            array('support_dt','date','format'=>'dd.MM.yyyy HH:mm:ss'),
            array('support_callback_dt','date','format'=>'dd.MM.yyyy HH:mm:ss'),
			array('id, sim_id, number, personal_account, status, balance_status, balance_status_changed_dt, codeword, service_password, short_number, support_operator_id, support_operator_got_dt, support_dt, support_status, support_callback_dt, support_callback_name, support_getting_passport_variant, support_number_region_usage, support_sent_sms_status, user_id, support_passport_need_validation', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'balanceReportNumbers' => array(self::HAS_MANY, 'BalanceReportNumber', 'number_id'),
			'bonusReportNumbers' => array(self::HAS_MANY, 'BonusReportNumber', 'number_id'),
			'sim' => array(self::BELONGS_TO, 'Sim', 'sim_id'),
			'supportOperator' => array(self::BELONGS_TO, 'SupportOperator', 'support_operator_id'),
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
			'numberHistories' => array(self::HAS_MANY, 'NumberHistory', 'number_id'),
			'subscriptionAgreements' => array(self::HAS_MANY, 'SubscriptionAgreement', 'number_id'),
			'tickets' => array(self::HAS_MANY, 'Ticket', 'number_id'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'sim_id' => null,
			'number' => Yii::t('app', 'Number'),
			'personal_account' => Yii::t('app', 'Personal Account'),
			'status' => Yii::t('app', 'Status'),
			'balance_status' => Yii::t('app', 'Balance Status'),
			'balance_status_changed_dt' => Yii::t('app', 'Balance Status Changed Dt'),
			'codeword' => Yii::t('app', 'Codeword'),
			'service_password' => Yii::t('app', 'Service Password'),
			'short_number' => Yii::t('app', 'Short Number'),
			'support_operator_id' => null,
			'support_operator_got_dt' => Yii::t('app', 'Support Operator Got Dt'),
			'support_dt' => Yii::t('app', 'Support Dt'),
			'support_status' => Yii::t('app', 'Support Status'),
			'support_callback_dt' => Yii::t('app', 'Support Callback Dt'),
			'support_callback_name' => Yii::t('app', 'Support Callback Name'),
			'support_getting_passport_variant' => Yii::t('app', 'Support Getting Passport Variant'),
			'support_number_region_usage' => Yii::t('app', 'Support Number Region Usage'),
			'support_sent_sms_status' => Yii::t('app', 'Support Sent Sms Status'),
			'user_id' => null,
			'support_passport_need_validation' => Yii::t('app', 'Support Passport Need Validation'),
			'balanceReportNumbers' => null,
			'bonusReportNumbers' => null,
			'sim' => null,
			'supportOperator' => null,
			'user' => null,
			'numberHistories' => null,
			'subscriptionAgreements' => null,
			'tickets' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('sim_id', $this->sim_id);
		$criteria->compare('number', $this->number, true);
		$criteria->compare('personal_account', $this->personal_account, true);
		$criteria->compare('status', $this->status, true);
		$criteria->compare('balance_status', $this->balance_status, true);
		$criteria->compare('balance_status_changed_dt', $this->balance_status_changed_dt, true);
		$criteria->compare('codeword', $this->codeword, true);
		$criteria->compare('service_password', $this->service_password, true);
		$criteria->compare('short_number', $this->short_number, true);
		$criteria->compare('support_operator_id', $this->support_operator_id);
		$criteria->compare('support_operator_got_dt', $this->support_operator_got_dt, true);
		$criteria->compare('support_dt', $this->support_dt, true);
		$criteria->compare('support_status', $this->support_status, true);
		$criteria->compare('support_callback_dt', $this->support_callback_dt, true);
		$criteria->compare('support_callback_name', $this->support_callback_name, true);
		$criteria->compare('support_getting_passport_variant', $this->support_getting_passport_variant, true);
		$criteria->compare('support_number_region_usage', $this->support_number_region_usage, true);
		$criteria->compare('support_sent_sms_status', $this->support_sent_sms_status, true);
		$criteria->compare('user_id', $this->user_id);
		$criteria->compare('support_passport_need_validation', $this->support_passport_need_validation);

		$dataProvider=new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));

        $dataProvider->pagination->pageSize=self::ITEMS_PER_PAGE;
        return $dataProvider;
	}

    public function convertDateTimeFieldsToEDateTime() {
        // rest of work will do setAttribute() routine
        $this->setAttribute('balance_status_changed_dt',strval($this->balance_status_changed_dt));
        $this->setAttribute('support_operator_got_dt',strval($this->support_operator_got_dt));
        $this->setAttribute('support_dt',strval($this->support_dt));
        $this->setAttribute('support_callback_dt',strval($this->support_callback_dt));
    }

    public function convertDateTimeFieldsToString() {
        if (is_object($this->balance_status_changed_dt) && get_class($this->balance_status_changed_dt)=='EDateTime') $this->balance_status_changed_dt=new EString($this->balance_status_changed_dt->format(self::$mySqlDateTimeFormat));
        if (is_object($this->support_operator_got_dt) && get_class($this->support_operator_got_dt)=='EDateTime') $this->support_operator_got_dt=new EString($this->support_operator_got_dt->format(self::$mySqlDateTimeFormat));
        if (is_object($this->support_dt) && get_class($this->support_dt)=='EDateTime') $this->support_dt=new EString($this->support_dt->format(self::$mySqlDateTimeFormat));
        if (is_object($this->support_callback_dt) && get_class($this->support_callback_dt)=='EDateTime') $this->support_callback_dt=new EString($this->support_callback_dt->format(self::$mySqlDateTimeFormat));
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
            if ($name=='balance_status_changed_dt') $value=$this->convertStringToEDateTime($value,'datetime');
            if ($name=='support_operator_got_dt') $value=$this->convertStringToEDateTime($value,'datetime');
            if ($name=='support_dt') $value=$this->convertStringToEDateTime($value,'datetime');
            if ($name=='support_callback_dt') $value=$this->convertStringToEDateTime($value,'datetime');
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