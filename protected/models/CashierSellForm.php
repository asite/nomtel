<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pavel
 * Date: 28.04.13
 * Time: 13:38
 * To change this template use File | Settings | File Templates.
 */
class CashierSellForm extends CFormModel
{
    const TYPE_CLIENT='CLIENT';
    const TYPE_AGENT='AGENT';

    const PAYMENT_CASH=0;
    const PAYMENT_NOT_CASH=1;

    public $type;
    public $agent_id;
    public $payment;
    public $sum;
    public $comment;

    public function rules() {
        return array(
            array('type,sum,payment','required'),
            array('agent_id,comment','safe'),
            array('sum','numerical'),
            array('agent_id','required','on'=>'agent_id'),
            array('agent_id,comment','required','on'=>'agent_id_comment'),
            array('comment','required','on'=>'comment'),
        );
    }

    public function attributeLabels() {
        return array(
            'agent_id'=>Agent::label(),
            'type'=>'Продажа',
            'sum'=>'Стоимость операции',
            'comment'=>'комментарий',
            'payment'=>'Оплата'
        );
    }

    public function getTypeList() {
        return array(
            self::TYPE_CLIENT=>'Розничная',
            self::TYPE_AGENT=>'Aгенту'
        );
    }

    public function getPaymentList() {
        return array(
            self::PAYMENT_CASH=>'Наличными',
            self::PAYMENT_NOT_CASH=>'Другое'
        );
    }
}
