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
	$.fn.yiiGridView.update('agent-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo GxHtml::encode($model->label(2)); ?></h1>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'bonusReport-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter' => $model,
    'columns' => array(
        'id',
        'dt',
        array(
            'name'=>'operator_id',
            'value'=>'$data->operator',
            'filter'=>Operator::getComboList(),
        ),
        'comment',
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'htmlOptions' => array('style'=>'width:80px;text-align:center;vertical-align:middle'),
            'template'=>'{view} {delete}',
            'buttons'=>array(
                'delete'=>array(
                    'visible'=>"isAdmin()"
                )
            ),
            'deleteConfirmation' => Yii::t('app','Are you sure to delete this Bonus Report?'),
        ),
    ),
)); ?>