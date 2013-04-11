<?php

abstract class BulkOperation
{
    const MAX_SQL_SIZE = 204800; //200kb

    private $sqlHeader;
    protected $sqlData;
    private $sqlFooter;
    private $sqlMaxDataSize;


    public function __construct()
    {
        $this->sqlHeader = $this->getSqlHeader();
        $this->sqlFooter .= $this->getSqlFooter();
        $this->sqlMaxDataSize = self::MAX_SQL_SIZE - strlen($this->sqlHeader) - strlen($this->sqlFooter);
    }

    protected function flush($force)
    {
        if ($this->sqlData == '') return;

        if ($force || strlen($this->sqlData) > $this->sqlMaxDataSize) {
            $sql=$this->sqlHeader . $this->sqlData . $this->sqlFooter;
            $this->sqlData = '';
            Yii::app()->db->createCommand($sql)->execute();
        }
    }

    abstract protected function getSqlHeader();
    abstract protected function getSqlFooter();

    public function finish()
    {
        $this->flush(true);
    }

    public function __destruct()
    {
        if ($this->sqlData != '') throw new Exception('Bulk operation was not finished with finish() method');
    }

}
