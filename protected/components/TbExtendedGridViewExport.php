<?php

Yii::import('bootstrap.widgets.TbExtendedGridView');

class TbExtendedGridViewExport extends TbExtendedGridView {

    public function init()
    {
        if ($_REQUEST['exportGridView']==$this->id) {
            foreach ($this->columns as $k=>$v) {
                if ($v['class']=='bootstrap.widgets.TbButtonColumn') unset($this->columns[$k]);
            }
            if (isset($this->bulkActions)) $this->bulkActions='';
            $this->dataProvider->pagination->setPageSize(1000);
        }
        parent::init();
    }

    private function export($array, $step, $col_sep=";", $qut='"', $row_sep="\n") {
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
            if ($tmp) $output .= substr($tmp, 1).$row_sep;
        }

        return $output;
    }

    public function renderContent() {
        if ($_REQUEST['exportGridView']==$this->id) {
            ob_end_clean();
            $pageCount = floor(round($this->dataProvider->totalItemCount/$this->dataProvider->itemCount));
            $csv = '';
            for ($i=0;$i<$pageCount;$i++) {
                $this->dataProvider->pagination->setCurrentPage($i);
                $this->dataProvider->getData(true);
                ob_start();
                parent::renderContent();

                $export = ob_get_contents();
                ob_end_clean();
                $csv.=$this->export($export,$i);
            }
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=".str_replace('/','_',Yii::app()->controller->route).".csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            echo mb_convert_encoding($csv,'Windows-1251','UTF-8');

            // disable web logging
            foreach (Yii::app()->log->routes as $route) {
                if ($route instanceof CWebLogRoute || $route instanceof CProfileLogRoute) {
                    $route->enabled = false;
                }
            }
            Yii::app()->db->enableProfiling = false;

            Yii::app()->end();
        } else parent::renderContent();
    }

    public function renderSummary() {
        echo '<div class="gridSummary cfix">';
        echo '<a onclick="return createFiltersForm(\''.$this->id.'\',\''.Yii::app()->request->csrfToken.'\')" style="float: left;" class="btn btn-small">Сформировать отчет</a>';
        parent::renderSummary();
        echo '</div>';
    }

    public function registerClientScript(){

        //if ajaxUrl not set, default to the current action
        if(!isset($this->ajaxUrl))
            $this->ajaxUrl = '/'.Yii::app()->request->getPathInfo();

        //call parent function
        parent::registerClientScript();
    }
}