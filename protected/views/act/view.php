<?php

$this->breadcrumbs = array(
    Act::model()->label(2)=>array('list'),
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
        'comment',
    ),
)); ?>

<h2><?php echo GxHtml::encode(Sim::model()->label(2)); ?></h2>

<?php $this->widget('TbExtendedGridViewExport', array(
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
            'value'=>'$data->operator',
            'htmlOptions' => array('style'=>'text-align:center;'),
            'filter'=>Operator::getComboList(),
        ),
        array(
            'name'=>'tariff_id',
            'value'=>'$data->tariff',
            'htmlOptions' => array('style'=>'text-align:center;'),
            'filter'=>Tariff::getComboList(),
        ),
        array(
            'name'=>'number_price',
            'htmlOptions' => array('style'=>'text-align:center;'),
            'filter'=>false,
            ),
        array(
            'name'=>'sim_price',
            'htmlOptions' => array('style'=>'text-align:center;'),
            'filter'=>false,
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'htmlOptions' => array('style'=>'width:80px;text-align:center;vertical-align:middle'),
            'template'=>'{feedback}',
            'buttons'=>array(
                'feedback'=>array(
                    'label'=>Yii::t('app','Report problem'),
                    'icon'=>'envelope',
                    'url'=>'Yii::app()->controller->createUrl("act/report",array("id"=>$data->id))',
                    'visible'=>'!isAdmin()'
                )
            )
        ),
    ),
)); ?>