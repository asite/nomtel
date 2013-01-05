<?php

/**
 * This is the model base class for the table "act".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Act".
 *
 * Columns in table "act" available as properties of the model,
 * followed by relations of table "act" available as properties of the model.
 *
 * @property integer $id
 * @property integer $agent_id
 * @property string $type
 * @property string $dt
 * @property double $sum
 * @property string $comment
 *
 * @property Agent $agent
 * @property Sim[] $sims
 * @property Sim[] $sims1
 */
abstract class BaseAct extends BaseGxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'act';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'Act|Acts', $n);
	}

	public static function representingColumn() {
		return 'type';
	}

	public function rules() {
		return array(
			array('agent_id, type, dt, sum', 'required'),
			array('agent_id', 'numerical', 'integerOnly'=>true),
			array('sum', 'numerical'),
			array('type', 'length', 'max'=>6),
			array('comment', 'safe'),
			array('comment', 'default', 'setOnEmpty' => true, 'value' => null),
            array('dt','date','format'=>'dd.MM.yyyy HH:mm:ss'),
			array('id, agent_id, type, dt, sum, comment', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'agent' => array(self::BELONGS_TO, 'Agent', 'agent_id'),
			'sims' => array(self::HAS_MANY, 'Sim', 'parent_act_id'),
			'sims1' => array(self::HAS_MANY, 'Sim', 'act_id'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'agent_id' => null,
			'type' => Yii::t('app', 'Type'),
			'dt' => Yii::t('app', 'Dt'),
			'sum' => Yii::t('app', 'Sum'),
			'comment' => Yii::t('app', 'Comment'),
			'agent' => null,
			'sims' => null,
			'sims1' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('agent_id', $this->agent_id);
		$criteria->compare('type', $this->type, true);
		$criteria->compare('dt', $this->dt, true);
		$criteria->compare('sum', $this->sum);
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