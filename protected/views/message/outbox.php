<?php
  $this->breadcrumbs = array(
    Yii::t('app','Outbox'),
  );
?>

<h1><?php echo Yii::t('app','Outbox') ?></h1>

<?php
$this->widget('bootstrap.widgets.TbGridView', array(
  'itemsCssClass' => 'table table-striped table-bordered table-condensed',
  'id' => 'outbox-grid',
  'dataProvider' => $dataProvider,
  'columns' => array(
    array(
      'name'=>'id',
      'sortable'=>true,
      'htmlOptions' => array('style'=>'text-align:center;vertical-align:middle'),
      'header'=>Yii::t('app','ID'),
    ),
    array(
      'name'=>'whom',
      'sortable'=>true,
      'value'=>'$data->whom0',
      'htmlOptions' => array('style'=>'text-align:left;vertical-align:middle'),
      'header'=>Yii::t('app','To'),
    ),
    array(
      'name'=>'dt',
      'sortable'=>true,
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
      'template'=>'{view}',
      'buttons'=>array(
        'view'=>array(
          'url'=>'Yii::app()->createUrl("message/view",array("id"=>$data->id, "type"=>"outbox"))',
        ),
      ),
    ),

    /*array(
      'class' => 'bootstrap.widgets.TbButtonColumn',
      'template'=>'{view} {close}',
      'buttons'=>array(
        'params' => array('YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
        'close'=>array(
          'label'=>Yii::t('app','close the issue'),
          'icon'=>'check',
          'url'=>'Yii::app()->createUrl("message/close",array("id"=>$data->id))',
          'options' => array('ajax'=>array('type'=>'get','url'=>'js:$(this).attr("href")','success'=>'js:function(data){$.fn.yiiGridView.update("outbox-grid")}')),
        ),
      ),
      'htmlOptions' => array('style'=>'width:80px;text-align:center;vertical-align:middle'),
    ),*/
  ),
));

?>

<a class="btn" style="margin-bottom:10px;" href="<?php echo $this->createUrl('create'); ?>"><?php echo Yii::t('app', 'Create message'); ?></a>