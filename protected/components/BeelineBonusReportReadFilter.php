<?php

class BeelineBonusReportReadFilter implements PHPExcel_Reader_IReadFilter
{
    public function readCell($column, $row, $worksheetName = '')
    {
        return $column == 'D' || $column == 'H' || $column == 'U';
    }
}

