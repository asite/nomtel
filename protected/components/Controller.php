<?php

class Controller extends CController {

    public $layout='/layout/main';

    public $breadcrumbs;

    public function filters() {
        return array(
            array('AuthFilter -login'),
        );
    }

    public function setBreadcrumbs($breadcrumbs) {
        $this->breadcrumbs = $breadcrumbs;
    }
}

