<?php

class MegafonBalanceReportReadFilter implements PHPExcel_Reader_IReadFilter
{
    public function readCell($column, $row, $worksheetName = '')
    {
        return $column == 'A' || $column == 'F' || $column == 'M';
    }
}

