<?php

/**
 * This is the model base class for the table "number_last_info".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "NumberLastInfo".
 *
 * Columns in table "number_last_info" available as properties of the model,
 * and there are no model relations.
 *
 * @property string $number_id
 * @property string $dt
 * @property string $text
 *
 */
abstract class BaseNumberLastInfo extends BaseGxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'number_last_info';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'NumberLastInfo|NumberLastInfos', $n);
	}

	public static function representingColumn() {
		return 'dt';
	}

	public function rules() {
		return array(
			array('number_id, dt, text', 'required'),
			array('number_id', 'length', 'max'=>20),
            array('dt','date','format'=>'dd.MM.yyyy HH:mm:ss'),
			array('number_id, dt, text', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'number_id' => Yii::t('app', 'Number'),
			'dt' => Yii::t('app', 'Dt'),
			'text' => Yii::t('app', 'Text'),
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('number_id', $this->number_id, true);
		$criteria->compare('dt', $this->dt, true);
		$criteria->compare('text', $this->text, true);

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