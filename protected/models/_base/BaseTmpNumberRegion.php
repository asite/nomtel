<?php

/**
 * This is the model base class for the table "tmp_number_region".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "TmpNumberRegion".
 *
 * Columns in table "tmp_number_region" available as properties of the model,
 * and there are no model relations.
 *
 * @property integer $id
 * @property string $start
 * @property string $end
 * @property integer $operator_id
 * @property integer $region_id
 * @property string $region
 *
 */
abstract class BaseTmpNumberRegion extends BaseGxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'tmp_number_region';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'TmpNumberRegion|TmpNumberRegions', $n);
	}

	public static function representingColumn() {
		return 'start';
	}

	public function rules() {
		return array(
			array('start, end, operator_id, region_id, region', 'required'),
			array('operator_id, region_id', 'numerical', 'integerOnly'=>true),
			array('start, end', 'length', 'max'=>50),
			array('region', 'length', 'max'=>256),
			array('id, start, end, operator_id, region_id, region', 'safe', 'on'=>'search'),
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
			'id' => Yii::t('app', 'ID'),
			'start' => Yii::t('app', 'Start'),
			'end' => Yii::t('app', 'End'),
			'operator_id' => Yii::t('app', 'Operator'),
			'region_id' => Yii::t('app', 'Region'),
			'region' => Yii::t('app', 'Region'),
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('start', $this->start, true);
		$criteria->compare('end', $this->end, true);
		$criteria->compare('operator_id', $this->operator_id);
		$criteria->compare('region_id', $this->region_id);
		$criteria->compare('region', $this->region, true);

		$dataProvider=new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));

        $dataProvider->pagination->pageSize=self::ITEMS_PER_PAGE;
        return $dataProvider;
	}

}