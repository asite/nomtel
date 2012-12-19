<?php

/**
 * This is the model base class for the table "operator".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Operator".
 *
 * Columns in table "operator" available as properties of the model,
 * followed by relations of table "operator" available as properties of the model.
 *
 * @property integer $id
 * @property string $title
 *
 * @property Sim[] $sims
 * @property Tariff[] $tariffs
 */
abstract class BaseOperator extends BaseGxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'operator';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'Operator|Operators', $n);
	}

	public static function representingColumn() {
		return 'title';
	}

	public function rules() {
		return array(
			array('title', 'required'),
			array('title', 'length', 'max'=>200),
			array('id, title', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'sims' => array(self::HAS_MANY, 'Sim', 'operator_id'),
			'tariffs' => array(self::HAS_MANY, 'Tariff', 'operator_id'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'title' => Yii::t('app', 'Title'),
			'sims' => null,
			'tariffs' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('title', $this->title, true);

		$dataProvider=new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));

        $dataProvider->pagination->pageSize=self::ITEMS_PER_PAGE;
        return $dataProvider;
	}

}