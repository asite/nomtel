<?php
  $this->breadcrumbs = array(
    Yii::t('app','message to agent up'),
  );
?>

<h1><?php echo Yii::t('app','message to agent up') ?></h1>

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
      'name'=>'title',
      'sortable'=>true,
      'header'=>Yii::t('app','Theme'),
    ),
    array(
      'name'=>'date',
      'sortable'=>true,
      'value'=>'new EDateTime($data["date"])',
      'htmlOptions' => array('style'=>'text-align:center;vertical-align:middle'),
      'header'=>Yii::t('app','Date'),
    ),
    array(
      'name'=>'status',
      'sortable'=>true,
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

<a class="btn" style="margin-bottom:10px;" href="<?php echo $this->createUrl('createTicket',array('to'=>'agentUp')); ?>"><?php echo Yii::t('app', 'Create ticket') ?></a>