<?php

$this->breadcrumbs = array(
    BonusReport::model()->label(2) => array('list'),
    $parentModel->adminLabel($parentModel->label(1)),
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

<h1><?php echo GxHtml::encode($model->label(2)); ?></h1>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'payment-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    //'filter' => $model,
    'columns' => array(
        array(
            'name'=>'agent_id',
            'value'=>'$data->agent',
            'filter'=>Agent::getComboList()
        ),
        'sum'
    ),
)); ?>