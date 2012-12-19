<?php

$this->breadcrumbs = array(
	$model->label(2) => array('list'),
	Yii::t('app', 'List'),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('delivery-report-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo GxHtml::encode($model->label(2)); ?></h1>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id' => 'delivery-report-grid',
	'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter' => $model,
	'columns' => array(
        array(
                'name'=>'id',
                'htmlOptions' => array('style'=>'width:80px;text-align:center;vertical-align:middle'),
             ),
		array(
				'name'=>'agent_id',
				'value'=>'GxHtml::valueEx($data->agent)',
				'filter'=>Agent::getComboList(),
                'visible'=>Yii::app()->user->getState('isAdmin'),
		),
        array(
		        'name'=>'dt',
                'filter'=>false,
                'htmlOptions' => array('style'=>'text-align:center;'),
             ),
        array(
		        'name'=>'sum',
                'filter'=>false,
                'htmlOptions' => array('style'=>'text-align:center;'),
            ),
		array(
			'class' => 'bootstrap.widgets.TbButtonColumn',
            'htmlOptions' => array('style'=>'width:80px;text-align:center;vertical-align:middle'),
			'template'=>'{view}',
		),
	),
)); ?>