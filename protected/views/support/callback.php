<?php

$this->breadcrumbs = array(
    Yii::t('app','Call back')
);
?>

<h1><?=Yii::t('app','Call back')?></h1>

<?php
$this->widget('bootstrap.widgets.TbGridView', array(
  'id' => 'number-grid',
  'itemsCssClass' => 'table table-striped table-bordered table-condensed',
  'dataProvider' => $dataProvider,
  'filter'=>$model,
  'columns' => array(
    array(
      'name'=>'number',
      'sortable'=>true,
      'value'=>'$data->number',
      'htmlOptions' => array('style'=>'text-align:left;vertical-align:middle'),
      'header'=>Yii::t('app','Number')
    ),
    array(
      'name'=>'support_callback_name',
      'sortable'=>true,
      'value'=>'$data->support_callback_name',
      'htmlOptions' => array('style'=>'text-align:left;vertical-align:middle'),
      'header'=>Yii::t('app','Name')
    ),
    array(
      'name'=>'support_callback_dt',
      'sortable'=>true,
      'value'=>'$data->support_callback_dt',
      'htmlOptions' => array('style'=>'text-align:left;vertical-align:middle'),
      'header'=>Yii::t('app','Date')
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