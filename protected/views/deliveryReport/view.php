<?php

$this->breadcrumbs = array(
    DeliveryReport::model()->label(2)=>array('list'),
    $model->adminLabel($model->label(1)),
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

<?php
$this->widget('bootstrap.widgets.TbAlert', array(
'block'=>true, // display a larger alert block?
'fade'=>true, // use transitions?
'closeText'=>'×', // close link text - if set to false, no close link is displayed
'alerts'=>array( // configurations per alert type
'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'×'), // success, info, warning, error or danger
),
));
?>

<h1><?php echo GxHtml::encode($model->label(1)); ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
    'data'=>$model,
    'attributes'=>array(
        'id',
        array(
            'name'=>'agent_id',
            'value'=>$model->agent
        ),
        'dt',
        'sum',
    ),
)); ?>

<h2><?php echo GxHtml::encode(Sim::model()->label(2)); ?></h2>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'delivery-report-grid',
    'dataProvider' => $sim->search(),
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter' => $sim,
    'columns' => array(
        array(
            'name'=>'number',
            'htmlOptions' => array('style'=>'text-align:center;'),
        ),
        array(
            'name'=>'icc',
            'value'=>'$data->shortIcc',
            'htmlOptions' => array('style'=>'text-align:center;'),
        ),
        array(
            'name'=>'operator_id',
            'value'=>'$data->operator->title',
            'sortable'=>false,
            'htmlOptions' => array('style'=>'text-align:center;'),
        ),
        array(
            'name'=>'tariff_id',
            'value'=>'$data->tariff->title',
            'sortable'=>false,
            'htmlOptions' => array('style'=>'text-align:center;'),
        ),
        array(
            'name'=>'number_price',
            'sortable'=>false,
            'htmlOptions' => array('style'=>'text-align:center;'),
            ),
        array(
            'class' => 'bootstrap.widgets.TbDataColumn',
            'header' => Yii::t('app','Sim Price'),
            'value' => '$data->deliveryReport->sim_price',
            'sortable'=>true,
            'htmlOptions' => array('style'=>'text-align:center;'),
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'htmlOptions' => array('style'=>'width:80px;text-align:center;vertical-align:middle'),
            'template'=>'{feedback}',
            'buttons'=>array(
                'feedback'=>array(
                    'label'=>Yii::t('app','Report problem'),
                    'icon'=>'envelope',
                    'url'=>'Yii::app()->controller->createUrl("deliveryReport/report",array("id"=>$data->id))'
                )
            )
        ),
    ),
)); ?>