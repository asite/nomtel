<?php

Yii::import('bootstrap.widgets.TbExtendedGridView');

class TbExtendedGridViewExport extends TbExtendedGridView {

    public function init()
    {
        if ($_POST['exportGridView']==$this->id) {
            foreach ($this->columns as $k=>$v) {
                if ($v['class']=='bootstrap.widgets.TbButtonColumn') unset($this->columns[$k]);
            }
            if (isset($this->bulkActions)) $this->bulkActions='';
            $this->dataProvider->pagination->setPageSize(1000);
        }
        parent::init();
    }
    public function renderContent() {
        if ($_POST['exportGridView']==$this->id) {
            ob_end_clean();
            $pageCount = floor(round($this->dataProvider->totalItemCount/$this->dataProvider->itemCount));
            $csv = '';
            for ($i=0;$i<$pageCount;$i++) {
                $this->dataProvider->pagination->setCurrentPage($i);
                ob_start();
                parent::renderContent();
                $export = ob_get_contents();
                ob_end_clean();
                $csv.=Controller::export($export,$i);
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

}