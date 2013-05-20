<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pavel
 * Date: 28.04.13
 * Time: 13:38
 * To change this template use File | Settings | File Templates.
 */
class CashierRestoreForm extends CFormModel
{
    public $icc;

    public function rules() {
        return array(
            array('icc','required'),
        );
    }

    public function attributeLabels() {
        return array(
            'icc'=>'Icc',
        );
    }
}
