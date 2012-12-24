<?php

class MegafonBonusReportReadFilter implements PHPExcel_Reader_IReadFilter
{
    public function readCell($column, $row, $worksheetName = '')
    {
        return $column == 'A' || $column == 'I';
    }
}

