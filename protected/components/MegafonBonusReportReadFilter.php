<?php

class MegafonBonusReportReadFilter implements PHPExcel_Reader_IReadFilter
{
    public function readCell($column, $row, $worksheetName = '')
    {
        if ($worksheetName!='Форма Новая') return false;
        return $column == 'B' || $column == 'J';
    }
}

