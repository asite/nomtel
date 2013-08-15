<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pavel
 * Date: 15.08.13
 * Time: 14:56
 * To change this template use File | Settings | File Templates.
 */

class CashierNumberRestore1 extends CFormModel {
    public $sim_type;
    public $contact_phone;
    public $contact_name;
    public $payment;
    public $sum;

    const PAYMENT_IMMEDIATE='IMMEDIATE';
    const PAYMENT_WHEN_DONE='WHEN_DONE';

    public function rules() {
        return array(
            array('sim_type,contact_name,contact_phone,payment','required'),
            array('sum','required','on'=>'with_sum'),
            array('sum','numerical')
        );
    }

    public function attributeLabels() {
        return array(
			'sim_type' => Yii::t('app', 'Sim Type'),
			'payment' => Yii::t('app', 'Payment'),
			'sum' => Yii::t('app', 'Sum'),
			'contact_phone' => Yii::t('app', 'Contact Phone'),
			'contact_name' => Yii::t('app', 'Contact Name'),
        );
    }

    public static function getPaymentRadioList($items=array()) {
        $labels=self::getPaymentLabels();
        return array_merge($items,$labels);
    }

    public static function getPaymentLabels() {
        static $labels;

        if (!$labels) {
            $labels=array(
                self::PAYMENT_IMMEDIATE=>Yii::t('app','PAYMENT_TYPE_IMMEDIATE'),
                self::PAYMENT_WHEN_DONE=>Yii::t('app','PAYMENT_TYPE_WHEN_DONE'),
            );
        }

        return $labels;
    }

    public static function getPaymentLabel($status) {
        $labels=self::getPaymentLabels();
        return $labels[$status];
    }


}