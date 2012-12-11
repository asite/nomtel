<?php

$this->breadcrumbs = array(
    Yii::t('app', 'List'),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('sim-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo GxHtml::encode($model->label(2)); ?></h1>

<a class="btn" style="margin-bottom:10px;" href="#" onclick="jQuery('#Sim_number').val('<?php echo Yii::t('app','WITHOUT NUMBER');?>').trigger(jQuery.Event('keydown', {keyCode: 13}));"><?php echo Yii::t('app', 'Without number') ?></a>

<?php $this->widget('bootstrap.widgets.TbExtendedGridView', array(
    'id' => 'sim-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter' => $model,
    'afterAjaxUpdate' => 'js:function(id,data){multiPageSelRestore(id)}',
    'columns' => array(
        array(
            'name'=>'delivery_report_id',
            'value'=>'$data->deliveryReport->dt ? $data->deliveryReport->dt->format("d.m.Y"):"";    ',
            'filter'=>false,
        ),
        array(
            'name'=>'number',
        ),
        array(
            'name'=>'icc',
        ),
        array(
            'name'=>'operator_id',
            'value'=>'$data->operator',
            'filter'=>Operator::getComboList(),
        ),
        array(
            'name'=>'tariff_id',
            'value'=>'$data->tariff',
            'filter'=>Tariff::getComboList(),
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'htmlOptions' => array('style'=>'width:40px;text-align:center;vertical-align:middle'),
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
