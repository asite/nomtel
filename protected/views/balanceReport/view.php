<?php

$this->breadcrumbs = array(
    $balanceReport::model()->label(2) => array('list'),
    $balanceReport->adminLabel($balanceReport->label(1)),
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

<h1><?php echo $balanceReport->adminLabel($balanceReport->label(1)); ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
    'data'=>$balanceReport,
    'attributes'=>array(
        'dt',
        'comment',
    ),
)); ?>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'balanceReportNumber-grid',
    'dataProvider' => $balanceReportNumberDataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter' => $balanceReportNumberSearch,
    'columns' => array(
        array(
            'name'=>'personal_account',
            'header'=>Yii::t('app','Personal Account')
        ),
        array(
            'name'=>'number',
            'header'=>Yii::t('app','Number')
        ),
        array(
            'name'=>'balance',
            'header'=>Yii::t('app','Balance')
        )
    ),
)); ?>