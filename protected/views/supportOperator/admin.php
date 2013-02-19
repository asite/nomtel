<?php

$this->breadcrumbs = array(
	$model->label(2) => array('admin'),
	Yii::t('app', 'List'),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('support-operator-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo GxHtml::encode($model->label(2)); ?></h1>

<a class="btn" style="margin-bottom:10px;" href="<?php echo $this->createUrl('create') ?>"><?php echo Yii::t('app', 'Create') ?></a>
<?php $this->widget('TbExtendedGridViewExport', array(
	'id' => 'support-operator-grid',
	'dataProvider' => $model->search(),
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter' => $model,
	'columns' => array(
		//'id',
		/*array(
				'name'=>'user_id',
				'value'=>'GxHtml::valueEx($data->user)',
				'filter'=>GxHtml::listDataEx(User::model()->findAllAttributes(null, true)),
				),*/
		'name',
		'surname',
		'middle_name',
		'phone',
        'email',
		array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'htmlOptions' => array('style'=>'width:80px;text-align:center;vertical-align:middle'),
            'template'=>'{view} {update} {delete}',
            'deleteConfirmation' => Yii::t('app','Are you sure to delete this Technical Support User?'),
        ),
	),
)); ?>