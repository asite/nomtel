<?php

$this->breadcrumbs = array(
	$model->label(2) => array('admin'),
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

<a class="btn" style="margin-bottom:10px;" href="<?php echo $this->createUrl('create') ?>"><?php echo Yii::t('app', 'Create') ?></a>
<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id' => 'agent-grid',
	'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter' => $model,
	'columns' => array(
        'surname',
		'name',
		'middle_name',
        /*
        'phone_1',
        'phone_2',
        'phone_3',
        'email',
        'skype',
        'icq',
        'passport_series',
        'passport_number',
        'passport_issue_date',
        'passport_issuer',
        'birthday_date',
        'birthday_place',
        'registration_address',
        'balance',
        */
        'balance',
		array(
			'class' => 'bootstrap.widgets.TbButtonColumn',
            'htmlOptions' => array('style'=>'width:80px;text-align:center;vertical-align:middle'),
			'template'=>'{view} {update} {delete}',
	        'deleteConfirmation' => Yii::t('app','Are you sure to delete this Agent?'),
		),
	),
)); ?>