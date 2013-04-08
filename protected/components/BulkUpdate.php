<?php

class BulkUpdate extends BulkOperation
{
    protected $table;
    protected $attributes;

    public function __construct($table, $attributes)
    {
        $this->table= $table;
        $this->attributes = $attributes;

        parent::__construct();
    }

    protected function getSqlHeader() {
        $sqlHeader = "insert into {$this->table} (";

        $quotedAttributes = array();
        $updateAttributes = array();
        foreach ($this->attributes as $attribute) {
            $quotedAttributes[] = Yii::app()->db->quoteColumnName($attribute);
            $updateAttributes[] = Yii::app()->db->quoteColumnName($attribute).
                '=values('.Yii::app()->db->quoteColumnName($attribute).')';
        }

        $sqlHeader .= implode(',', $quotedAttributes);
        $sqlHeader .= ') values ';

        return $sqlHeader;
    }

    protected function getSqlFooter() {
        $updateAttributes = array();
        foreach ($this->attributes as $attribute) {
            $updateAttributes[] = Yii::app()->db->quoteColumnName($attribute).
                '=values('.Yii::app()->db->quoteColumnName($attribute).')';
        }

        $sqlFooter = ' on duplicate key update '.implode(',', $updateAttributes);

        return $sqlFooter;
    }

    protected function addRowSql($row) {
        $rowSql = '';
        foreach ($this->attributes as $attribute) {
            if ($rowSql != '') $rowSql .= ',';
            $rowSql .= is_null($row[$attribute]) ? 'NULL':Yii::app()->db->quoteValue($row[$attribute]);
        }

        if ($this->sqlData != '') $this->sqlData .= ',';
        $this->sqlData .= "($rowSql)";

        $this->flush(false);
    }

    public function add($row) {
        $this->addRowSql($row);
    }
}