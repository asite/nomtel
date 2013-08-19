<?php

/**
 * This is the model base class for the table "cashier_sell_number".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "CashierSellNumber".
 *
 * Columns in table "cashier_sell_number" available as properties of the model,
 * followed by relations of table "cashier_sell_number" available as properties of the model.
 *
 * @property integer $id
 * @property string $dt
 * @property integer $support_operator_id
 * @property string $number_id
 * @property string $sum
 * @property string $cashier_debit_credit_id
 * @property string $comment
 *
 * @property CashierDebitCredit $cashierDebitCredit
 */
abstract class BaseCashierSellNumber extends BaseGxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'cashier_sell_number';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'CashierSellNumber|CashierSellNumbers', $n);
	}

	public static function representingColumn() {
		return 'dt';
	}

	public function rules() {
		return array(
			array('dt, support_operator_id, number_id, sum', 'required'),
			array('support_operator_id', 'numerical', 'integerOnly'=>true),
			array('number_id, cashier_debit_credit_id', 'length', 'max'=>20),
			array('sum', 'length', 'max'=>14),
			array('comment', 'length', 'max'=>200),
			array('cashier_debit_credit_id, comment', 'default', 'setOnEmpty' => true, 'value' => null),
            array('dt','date','format'=>'dd.MM.yyyy HH:mm:ss'),
			array('id, dt, support_operator_id, number_id, sum, cashier_debit_credit_id, comment', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'cashierDebitCredit' => array(self::BELONGS_TO, 'CashierDebitCredit', 'cashier_debit_credit_id'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'dt' => Yii::t('app', 'Dt'),
			'support_operator_id' => Yii::t('app', 'Support Operator'),
			'number_id' => Yii::t('app', 'Number'),
			'sum' => Yii::t('app', 'Sum'),
			'cashier_debit_credit_id' => null,
			'comment' => Yii::t('app', 'Comment'),
			'cashierDebitCredit' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('dt', $this->dt, true);
		$criteria->compare('support_operator_id', $this->support_operator_id);
		$criteria->compare('number_id', $this->number_id, true);
		$criteria->compare('sum', $this->sum, true);
		$criteria->compare('cashier_debit_credit_id', $this->cashier_debit_credit_id);
		$criteria->compare('comment', $this->comment, true);

		$dataProvider=new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));

        $dataProvider->pagination->pageSize=self::ITEMS_PER_PAGE;
        return $dataProvider;
	}

    public function convertDateTimeFieldsToEDateTime() {
        // rest of work will do setAttribute() routine
        $this->setAttribute('dt',strval($this->dt));
    }

    public function convertDateTimeFieldsToString() {
        if (is_object($this->dt) && get_class($this->dt)=='EDateTime') $this->dt=new EString($this->dt->format(self::$mySqlDateTimeFormat));
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
            if ($name=='dt') $value=$this->convertStringToEDateTime($value,'datetime');
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