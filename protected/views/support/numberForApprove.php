<?php

$this->breadcrumbs = array(
    Yii::t('app','Operator Numbers')
);
?>

<h1><?=Yii::t('app','Numbers for Approve')?></h1>

<?php
$this->widget('bootstrap.widgets.TbGridView', array(
  'id' => 'number-grid',
  'itemsCssClass' => 'table table-striped table-bordered table-condensed',
  'htmlOptions' => array('style'=>'width: 50%;'),
  'dataProvider' => $dataProvider,
  'filter'=>$model,
  'columns' => array(
    array(
      'name'=>'number',
      'sortable'=>true,
      'value'=>'$data->number',
      'htmlOptions' => array('style'=>'text-align: center;vertical-align:middle'),
      'header'=>Yii::t('app','Number')
    ),
      array(
          'class' => 'bootstrap.widgets.TbButtonColumn',
          'htmlOptions' => array('style'=>'width:80px;text-align:center;vertical-align:middle'),
          'template'=>'{update}',
          'buttons'=>array(
              'update'=>array(
                  'url'=>'Yii::app()->createUrl("support/numberStatus",array("number"=>$data->number))',
                  'label'=>Yii::t('app','Process'),
              ),
          ),
      ),

  ),
));

?>