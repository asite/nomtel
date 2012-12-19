<?php

/**
 * This is the model base class for the table "sim".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Sim".
 *
 * Columns in table "sim" available as properties of the model,
 * followed by relations of table "sim" available as properties of the model.
 *
 * @property string $id
 * @property string $state
 * @property string $personal_account
 * @property string $number
 * @property double $number_price
 * @property string $icc
 * @property string $parent_agent_id
 * @property string $agent_id
 * @property string $delivery_report_id
 * @property string $operator_id
 * @property string $tariff_id
 *
 * @property DeliveryReport $deliveryReport
 * @property Agent $parentAgent
 * @property Tariff $tariff
 * @property Operator $operator
 * @property Agent $agent
 */
abstract class BaseSim extends BaseGxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'sim';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'Sim|Sims', $n);
	}

	public static function representingColumn() {
		return 'state';
	}

	public function rules() {
		return array(
			array('state, personal_account, number_price', 'required'),
			array('number_price', 'numerical'),
			array('state', 'length', 'max'=>18),
			array('personal_account, number, icc', 'length', 'max'=>50),
			array('parent_agent_id, agent_id, delivery_report_id, operator_id, tariff_id', 'length', 'max'=>20),
			array('number, icc, parent_agent_id, agent_id, delivery_report_id, operator_id, tariff_id', 'default', 'setOnEmpty' => true, 'value' => null),
			array('id, state, personal_account, number, number_price, icc, parent_agent_id, agent_id, delivery_report_id, operator_id, tariff_id', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'deliveryReport' => array(self::BELONGS_TO, 'DeliveryReport', 'delivery_report_id'),
			'parentAgent' => array(self::BELONGS_TO, 'Agent', 'parent_agent_id'),
			'tariff' => array(self::BELONGS_TO, 'Tariff', 'tariff_id'),
			'operator' => array(self::BELONGS_TO, 'Operator', 'operator_id'),
			'agent' => array(self::BELONGS_TO, 'Agent', 'agent_id'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'state' => Yii::t('app', 'State'),
			'personal_account' => Yii::t('app', 'Personal Account'),
			'number' => Yii::t('app', 'Number'),
			'number_price' => Yii::t('app', 'Number Price'),
			'icc' => Yii::t('app', 'Icc'),
			'parent_agent_id' => null,
			'agent_id' => null,
			'delivery_report_id' => null,
			'operator_id' => null,
			'tariff_id' => null,
			'deliveryReport' => null,
			'parentAgent' => null,
			'tariff' => null,
			'operator' => null,
			'agent' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('state', $this->state, true);
		$criteria->compare('personal_account', $this->personal_account, true);
		$criteria->compare('number', $this->number, true);
		$criteria->compare('number_price', $this->number_price);
		$criteria->compare('icc', $this->icc, true);
		$criteria->compare('parent_agent_id', $this->parent_agent_id);
		$criteria->compare('agent_id', $this->agent_id);
		$criteria->compare('delivery_report_id', $this->delivery_report_id);
		$criteria->compare('operator_id', $this->operator_id);
		$criteria->compare('tariff_id', $this->tariff_id);

		$dataProvider=new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));

        $dataProvider->pagination->pageSize=self::ITEMS_PER_PAGE;
        return $dataProvider;
	}

}