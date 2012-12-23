<?php
  $this->breadcrumbs = array(
    Yii::t('app','Inbox'),
  );
?>

<h1><?php echo Yii::t('app','Inbox') ?></h1>

<?php
$this->widget('bootstrap.widgets.TbGridView', array(
  'itemsCssClass' => 'table table-striped table-bordered table-condensed',
  'dataProvider' => $dataProvider,
  'columns' => array(
    array(
      'name'=>'id',
      'sortable'=>true,
      'htmlOptions' => array('style'=>'text-align:center;vertical-align:middle'),
      'header'=>Yii::t('app','ID'),
    ),
    array(
      'name'=>'dt',
      'sortable'=>true,
      //'value'=>'new EDateTime($data["dt"])',
      'htmlOptions' => array('style'=>'text-align:center;vertical-align:middle'),
      'header'=>Yii::t('app','Date'),
    ),
    array(
      'name'=>'title',
      'sortable'=>true,

      'htmlOptions' => array('style'=>'text-align:left;vertical-align:middle'),
      'header'=>Yii::t('app','Message'),
    ),
    array(
      'name'=>'status',
      'sortable'=>true,
      'value'=>'Yii::t("app",$data->status)',
      'htmlOptions' => array('style'=>'text-align:center;vertical-align:middle'),
      'header'=>Yii::t('app','Status'),
    ),
    array(
      'class' => 'bootstrap.widgets.TbButtonColumn',
      'htmlOptions' => array('style'=>'width:80px;text-align:center;vertical-align:middle'),
      'template'=>'{view}',
    ),
  ),
));

?>