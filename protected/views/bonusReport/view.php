<?php

$this->breadcrumbs = array(
    $bonusReport::model()->label(2) => array('list'),
    $bonusReport->adminLabel($bonusReport->label(1)),
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

<h1><?php echo $bonusReport->adminLabel($bonusReport->label(1)); ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
    'data'=>$bonusReportAgent,
    'attributes'=>array(
        'sim_count',
        'sum',
        'sum_referrals',
    ),
)); ?>

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
        array(
            'name'=>'sim_count',
            'htmlOptions' => array('style'=>'text-align:center;'),
        ),
        array(
            'name'=>'sum',
            'htmlOptions' => array('style'=>'text-align:center;'),
        )
    ),
)); ?>