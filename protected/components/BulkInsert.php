<?php

class BulkInsert
{
    const MAX_SQL_SIZE = 204800; //200kb

    private $attributes;
    private $sqlHeader;
    private $sqlData;
    private $sqlMaxDataSize;

    public function __construct($table, $attributes)
    {
        $this->sqlHeader = "insert into $table (";

        $this->attributes = $attributes;

        $quotedAttributes = array();
        foreach ($attributes as $attribute) {
            $quotedAttributes[] = Yii::app()->db->quoteColumnName($attribute);
        }

        $this->sqlHeader .= implode(',', $quotedAttributes);
        $this->sqlHeader .= ') values ';
        $this->sqlMaxDataSize = self::MAX_SQL_SIZE - strlen($this->sqlHeader);
    }

    private function flush($force)
    {
        if ($this->sqlData == '') return;

        if ($force || strlen($this->sqlData) > $this->sqlMaxDataSize) {
            $sql=$this->sqlHeader . $this->sqlData;
            $this->sqlData = '';
            Yii::app()->db->createCommand($sql)->execute();
        }
    }

    public function insert($row)
    {
        $rowSql = '';
        foreach ($this->attributes as $attribute) {
            if ($rowSql != '') $rowSql .= ',';
            $rowSql .= Yii::app()->db->quoteValue($row[$attribute]);
        }

        if ($this->sqlData != '') $this->sqlData .= ',';
        $this->sqlData .= "($rowSql)";

        $this->flush(false);
    }

    public function finish()
    {
        $this->flush(true);
    }

    public function __destruct()
    {
        if ($this->sqlData != '') throw new Exception('Bulk Insert was not finished with finish() method');
    }
}
