<?php

class BaseGxActiveRecord extends GxActiveRecord {

    const ITEMS_PER_PAGE = 500;

    protected static $mySqlDateFormat = 'Y-m-d';
    protected static $mySqlDateTimeFormat = 'Y-m-d H:i:s';

    public function save($runValidation = true, $attributes = null) {
        if (!parent::save($runValidation, $attributes))
            throw new Exception(CVarDumper::dumpAsString($this->getErrors()));
        return true;
    }

    public function adminLabel($prefix = '') {
        $value = trim(GxHtml::valueEx($this));
        mb_ereg("^(.{1,50})[\s]", $value . ' ', $matches);
        if ($value != $matches[1])
            $value = $matches[1] . '...';

        return ($prefix != '' ? $prefix . ' ' : '') . " '" . $value . "'";
    }

}
