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
 * @property string $personal_account
 * @property string $number
 * @property double $number_price
 * @property double $sim_price
 * @property string $icc
 * @property string $parent_id
 * @property integer $parent_agent_id
 * @property integer $parent_act_id
 * @property integer $agent_id
 * @property integer $act_id
 * @property integer $operator_id
 * @property integer $tariff_id
 * @property integer $operator_region_id
 * @property integer $company_id
 *
 * @property Agent $parentAgent
 * @property Sim $parent
 * @property Sim[] $sims
 * @property Act $parentAct
 * @property Act $act
 * @property OperatorRegion $operatorRegion
 * @property Company $company
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
		return 'personal_account';
	}

	public function rules() {
		return array(
			array('personal_account', 'required'),
			array('parent_agent_id, parent_act_id, agent_id, act_id, operator_id, tariff_id, operator_region_id, company_id', 'numerical', 'integerOnly'=>true),
			array('number_price, sim_price', 'numerical'),
			array('personal_account, number, icc', 'length', 'max'=>50),
			array('parent_id', 'length', 'max'=>20),
			array('number, number_price, sim_price, icc, parent_id, parent_agent_id, parent_act_id, agent_id, act_id, operator_id, tariff_id, operator_region_id, company_id', 'default', 'setOnEmpty' => true, 'value' => null),
			array('id, personal_account, number, number_price, sim_price, icc, parent_id, parent_agent_id, parent_act_id, agent_id, act_id, operator_id, tariff_id, operator_region_id, company_id', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'parentAgent' => array(self::BELONGS_TO, 'Agent', 'parent_agent_id'),
			'parent' => array(self::BELONGS_TO, 'Sim', 'parent_id'),
			'sims' => array(self::HAS_MANY, 'Sim', 'parent_id'),
			'parentAct' => array(self::BELONGS_TO, 'Act', 'parent_act_id'),
			'act' => array(self::BELONGS_TO, 'Act', 'act_id'),
			'operatorRegion' => array(self::BELONGS_TO, 'OperatorRegion', 'operator_region_id'),
			'company' => array(self::BELONGS_TO, 'Company', 'company_id'),
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
			'personal_account' => Yii::t('app', 'Personal Account'),
			'number' => Yii::t('app', 'Number'),
			'number_price' => Yii::t('app', 'Number Price'),
			'sim_price' => Yii::t('app', 'Sim Price'),
			'icc' => Yii::t('app', 'Icc'),
			'parent_id' => null,
			'parent_agent_id' => null,
			'parent_act_id' => null,
			'agent_id' => null,
			'act_id' => null,
			'operator_id' => null,
			'tariff_id' => null,
			'operator_region_id' => null,
			'company_id' => null,
			'parentAgent' => null,
			'parent' => null,
			'sims' => null,
			'parentAct' => null,
			'act' => null,
			'operatorRegion' => null,
			'company' => null,
			'tariff' => null,
			'operator' => null,
			'agent' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('personal_account', $this->personal_account, true);
		$criteria->compare('number', $this->number, true);
		$criteria->compare('number_price', $this->number_price);
		$criteria->compare('sim_price', $this->sim_price);
		$criteria->compare('icc', $this->icc, true);
		$criteria->compare('parent_id', $this->parent_id);
		$criteria->compare('parent_agent_id', $this->parent_agent_id);
		$criteria->compare('parent_act_id', $this->parent_act_id);
		$criteria->compare('agent_id', $this->agent_id);
		$criteria->compare('act_id', $this->act_id);
		$criteria->compare('operator_id', $this->operator_id);
		$criteria->compare('tariff_id', $this->tariff_id);
		$criteria->compare('operator_region_id', $this->operator_region_id);
		$criteria->compare('company_id', $this->company_id);

		$dataProvider=new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));

        $dataProvider->pagination->pageSize=self::ITEMS_PER_PAGE;
        return $dataProvider;
	}

}