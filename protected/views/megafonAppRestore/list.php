<?php

$this->breadcrumbs = array(
	$model->label(2) => array('admin'),
	Yii::t('app', 'List'),
);
?>

<h1><?php echo GxHtml::encode($model->label(2)); ?></h1>

<? $this->widget('bootstrap.widgets.TbButton',array('url'=>$this->createUrl("downloadUnrestoredSummary"),'label'=>'Скачать отчет о невосстановленных номерах (кроме последнего отосланного отчета)'))?>

<?php /*
<a class="btn" style="margin-bottom:10px;" href="<?php echo $this->createUrl('create') ?>"><?php echo Yii::t('app', 'Create') ?></a>
*/ ?>
<?php $this->widget('bootstrap.widgets.TbExtendedGridView', array(
	'id' => 'megafon-app-restore-grid',
	'dataProvider' => $model->search(),
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    //'filter' => $model,
	'columns' => array(
        array(
            'name'=>'id',
            'htmlOptions'=>array('style'=>'width:80px;text-align:center')
        ),
        array(
            'name'=>'dt',
            'htmlOptions'=>array('style'=>'width:80px;text-align:center')
        ),
        array(
            'name'=>'numbers_count',
            'htmlOptions'=>array('style'=>'width:80px;text-align:center')
        ),
        array(
            'name'=>'unprocessed_numbers_count',
            'htmlOptions'=>array('style'=>'width:80px;text-align:center'),
            'header'=>Yii::t('app','Unprocessed Numbers Count')
        ),
		array(
			'class' => 'bootstrap.widgets.TbButtonColumn',
            'htmlOptions' => array('style'=>'width:80px;text-align:center;vertical-align:middle'),
			'template'=>'{download}',
            'buttons'=>array(
                'download'=>array(
                    'label'=>'Скачать',
                    'icon'=>'download',
                    'url'=>'Yii::app()->controller->createUrl("megafonAppRestore/download",array("id"=>$data->id))',
                )
            )
		),
	),
)); ?>

