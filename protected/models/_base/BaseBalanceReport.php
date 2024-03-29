<?php

/**
 * This is the model base class for the table "balance_report".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "BalanceReport".
 *
 * Columns in table "balance_report" available as properties of the model,
 * followed by relations of table "balance_report" available as properties of the model.
 *
 * @property integer $id
 * @property string $dt
 * @property integer $operator_id
 * @property string $comment
 *
 * @property Operator $operator
 * @property BalanceReportNumber[] $balanceReportNumbers
 */
abstract class BaseBalanceReport extends BaseGxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'balance_report';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'BalanceReport|BalanceReports', $n);
	}

	public static function representingColumn() {
		return 'dt';
	}

	public function rules() {
		return array(
			array('dt, operator_id, comment', 'required'),
			array('operator_id', 'numerical', 'integerOnly'=>true),
			array('comment', 'length', 'max'=>200),
            array('dt','date','format'=>'dd.MM.yyyy HH:mm:ss'),
			array('id, dt, operator_id, comment', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'operator' => array(self::BELONGS_TO, 'Operator', 'operator_id'),
			'balanceReportNumbers' => array(self::HAS_MANY, 'BalanceReportNumber', 'balance_report_id'),
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
			'operator_id' => null,
			'comment' => Yii::t('app', 'Comment'),
			'operator' => null,
			'balanceReportNumbers' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('dt', $this->dt, true);
		$criteria->compare('operator_id', $this->operator_id);
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