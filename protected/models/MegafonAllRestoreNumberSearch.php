<?php

class MegafonAllRestoreNumberSearch extends CFormModel {
    public $number;
    public $id;
    public $dt;

    public function rules() {
        return array(
            array('number,id,dt','safe')
        );
    }
}