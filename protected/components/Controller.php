<?php

class Controller extends CController {

    public $layout='/layout/main';

    public $breadcrumbs;

    public function filters()
    {
        return array(
            'accessControl'           // required to enable accessRules
        );
    }

    public function additionalAccessRules() {
        return array();
    }

    public function accessRules()
    {
        return array_merge(
            array(
                array('allow','roles'=>array('admin'))
            ),
            $this->additionalAccessRules(),
            array(
                array('deny','users' => array('*'))
            )
        );
    }

    public function setBreadcrumbs($breadcrumbs) {
        $this->breadcrumbs = $breadcrumbs;
    }
}

