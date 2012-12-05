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
	$.fn.yiiGridView.update('tariff-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo GxHtml::encode($model->label(2)); ?></h1>

<a class="btn" style="margin-bottom:10px;" href="<?php echo $this->createUrl('create') ?>"><?php echo Yii::t('app', 'Create') ?></a>
<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id' => 'tariff-grid',
	'dataProvider' => $model->search(),
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    //'filter' => $model,
	'columns' => array(
		'id',
		array(
				'name'=>'operator_id',
				'value'=>'GxHtml::valueEx($data->operator)',
				'filter'=>GxHtml::listDataEx(Operator::model()->findAllAttributes(null, true)),
				),
		'title',
		'price_agent_sim',
		'price_license_fee',
		array(
			'class' => 'bootstrap.widgets.TbButtonColumn',
            'htmlOptions' => array('style'=>'width:80px;text-align:center;vertical-align:middle'),
			'template'=>'{update} {delete}',
	        'deleteConfirmation' => Yii::t('app','Are you sure to delete this Tariff?'),
		),
	),
)); ?>