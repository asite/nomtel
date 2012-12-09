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
	$.fn.yiiGridView.update('sim-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo GxHtml::encode($model->label(2)); ?></h1>

<?php if (!Yii::app()->user->getState('isAdmin')) { ?>
<a class="btn" style="margin-bottom:10px;" href="#" onclick="jQuery('#Sim_agent_id').val('<?php echo Yii::t('app','WITHOUT AGENT');?>').trigger(jQuery.Event('keydown', {keyCode: 13}));""><?php echo Yii::t('app', 'Without agent') ?></a>
<?php } ?>
<a class="btn" style="margin-bottom:10px;" href="#" onclick="jQuery('#Sim_number').val('<?php echo Yii::t('app','WITHOUT NUMBER');?>').trigger(jQuery.Event('keydown', {keyCode: 13}));"><?php echo Yii::t('app', 'Without number') ?></a>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'sim-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter' => $model,
    'columns' => array(
        array(
            'name'=>'delivery_report_id',
            'value'=>'$data->deliveryReport->dt',
            'filter'=>false,
        ),
        array(
            'name'=>'agent_id',
            'value'=>'GxHtml::valueEx($data->agent)',
            'filter'=>array_merge(array(0=>Yii::t('app','WITHOUT AGENT')),Agent::getComboList()),
            'visible'=>Yii::app()->user->getState('isAdmin'),
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
            'htmlOptions' => array('style'=>'width:80px;text-align:center;vertical-align:middle'),
            'template'=>'',
        ),
    ),
)); ?>