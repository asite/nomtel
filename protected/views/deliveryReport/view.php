<?php

$this->breadcrumbs = array(
    Sim::model()->label(2)=>array('list'),
    $model->adminLabel($model->label(1)) => array('view','id'=>$model->id),
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

<h1><?php echo GxHtml::encode(Sim::model()->label(2)); ?></h1>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'delivery-report-grid',
    'dataProvider' => $sim->search(),
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter' => $sim,
    'columns' => array(
        'number',
        'icc',
        array(
            'name'=>'operator_id',
            'value'=>'$data->operator->title',
            'sortable'=>false,
        ),
        array(
            'name'=>'tariff_id',
            'value'=>'$data->tariff->title',
            'sortable'=>false,
        ),
        array(
            'name'=>'number_price',
            'sortable'=>false
            ),
        array(
            'class' => 'bootstrap.widgets.TbDataColumn',
            'header' => Yii::t('app','Sim Price'),
            'value' => '$data->deliveryReport->sim_price',
            'sortable'=>true,
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'htmlOptions' => array('style'=>'width:80px;text-align:center;vertical-align:middle'),
            'template'=>'',
        ),
    ),
)); ?>