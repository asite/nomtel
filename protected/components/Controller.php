<?php

class Controller extends CController
{

    public $layout = '/layout/main';

    public $breadcrumbs;

    public function filters()
    {
        return array(
            'accessControl', // required to enable accessRules
            'forcedPasswordChange'
        );
    }

    public function filterForcedPasswordChange($filterChain) {
        if ($this->route!='site/forcedPasswordChange' && Yii::app()->user->getState('require_password_change')) $this->redirect(array('site/forcedPasswordChange'));

        $filterChain->run();
    }

    public function additionalAccessRules()
    {
        return array();
    }

    public function accessRules()
    {
        return array_merge(
            array(
                array('allow', 'roles' => array('admin','supportSuper'))
            ),
            $this->additionalAccessRules(),
            array(
                array('deny', 'users' => array('*'))
            )
        );
    }

    public function setBreadcrumbs($breadcrumbs)
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    public function exportExel($dataProvider, $columns) {
        Yii::import('application.vendors.PHPExcel', true);

        // Cell caching to reduce memory usage.
        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize ' => '256MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle('Simple');
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(30);
        $dataProvider->pagination->setPageSize(1000);
        $pageCount = floor(round($dataProvider->totalItemCount/$dataProvider->itemCount))+1;

        $col=0;
        foreach($columns as $data) $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col++,1, $data['name']);

        $str=2;
        for($i=0; $i<$pageCount; $i++) {
            $dataProvider->pagination->setCurrentPage($i);
            $dataExel =  $dataProvider->getData(true);

            foreach($dataExel as $data) {
                $col=0;
                foreach($columns as $v) $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col++, $str, eval("return ".$v['value'].";"));
                $str++;
                //echo "$str ".memory_get_usage()."<br/>";
            }
        }

        //$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getStyle('A1:Z1')->getFont()->setBold(true);

        ob_end_clean();
        ob_start();

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="report_number.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function export($array, $step, $col_sep=";", $qut='"', $row_sep="\n") {
        if ($step==0) {
            $m=array();
            preg_match_all('%<th[^>]*>.*?</th>%i',$array,$m);
            foreach($m[0] as $v) {
                $output .= "$col_sep$qut".strip_tags(html_entity_decode($v)).$qut;
            }
            $output = substr($output, 1).$row_sep;
        }

        $m=array();
        preg_match_all('%<tr[^>]*>.*?</tr>%is',$array,$m);
        foreach($m[0] as $v) {
            preg_match_all('%<td[^>]*>(.*?)</td>%is',$v,$mm);
            $tmp = '';
            foreach($mm[0] as $vv) {
               $tmp .= "$col_sep$qut".strip_tags(str_replace('"','""',html_entity_decode($vv))).$qut;
            }
            $output .= substr($tmp, 1).$row_sep;
        }

        return $output;
    }
}

