<?php

/**
 * This is the model base class for the table "subscription_agreement_file".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "SubscriptionAgreementFile".
 *
 * Columns in table "subscription_agreement_file" available as properties of the model,
 * and there are no model relations.
 *
 * @property string $subscription_agreement_id
 * @property string $file_id
 *
 */
abstract class BaseSubscriptionAgreementFile extends BaseGxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'subscription_agreement_file';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'SubscriptionAgreementFile|SubscriptionAgreementFiles', $n);
	}

	public static function representingColumn() {
		return array(
			'subscription_agreement_id',
			'file_id',
		);
	}

	public function rules() {
		return array(
			array('subscription_agreement_id, file_id', 'required'),
			array('subscription_agreement_id, file_id', 'length', 'max'=>20),
			array('subscription_agreement_id, file_id', 'safe', 'on'=>'search'),
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
			'subscription_agreement_id' => null,
			'file_id' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('subscription_agreement_id', $this->subscription_agreement_id);
		$criteria->compare('file_id', $this->file_id);

		$dataProvider=new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));

        $dataProvider->pagination->pageSize=self::ITEMS_PER_PAGE;
        return $dataProvider;
	}

}