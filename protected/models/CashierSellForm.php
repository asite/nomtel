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
    const TYPE_BASE=0;
    const TYPE_AGENT=1;

    public $icc;
    public $type;
    public $agent_id;

    public function rules() {
        return array(
            array('icc,type','required'),
            array('agent_id','safe'),
            array('agent_id','required','on'=>'to_agent')
        );
    }

    public function attributeLabels() {
        return array(
            'icc'=>'Icc',
            'agent_id'=>Agent::label(),
            'type'=>'Продажа'
        );
    }

    public function getTypeList() {
        return array(
            self::TYPE_BASE=>'Розничная',
            self::TYPE_AGENT=>'Aгенту'
        );
    }
}
