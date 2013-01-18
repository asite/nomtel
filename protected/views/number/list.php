<?php

$this->breadcrumbs = array(
    Number::label(2),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('agent-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?=Number::label(2)?></h1>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'number-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter' => $model,
    'columns' => array(
        'personal_account',
        'number',
        array(
            'name'=>'status',
            'filter'=>Number::getStatusDropDownList(),
            'value'=>'Number::getStatusLabel($data->status)',
            'htmlOptions'=>array('style'=>'width:120px;'),
        ),
        array(
            'name'=>'warning',
            'filter'=>Number::getWarningDropDownList(),
            'value'=>'Number::getWarningLabel($data->warning)',
        ),
        array(
            'name'=>'warning_dt',
            'filter'=>false
        )
    ),
)); ?>