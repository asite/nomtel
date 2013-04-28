<?php

class CashierController extends BaseGxController
{
    public function additionalAccessRules() {
        return array(
            array('disallow', 'roles' => array('admin')),
            array('allow', 'roles' => array('cashier')),
        );
    }

    public function actionSell() {

    }

    public function actionRestore()  {

    }
}