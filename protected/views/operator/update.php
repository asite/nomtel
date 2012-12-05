<?php

$this->breadcrumbs = array(
	$model->label(2) => array('admin'),
	$model->adminLabel(Yii::t('app', 'Updating Operator')),
);

?>

<h1><?php echo $model->adminLabel(Yii::t('app', 'Updating Operator')); ?></h1>

<?php
$this->renderPartial('_form', array(
		'model' => $model));
?>

<h2><?php echo GxHtml::encode($model2->label(2)); ?></h2>

<a class="btn" style="margin-bottom:10px;" href="<?php echo $this->createUrl('tariff/create',array('parent_id'=>$model->id)) ?>"><?php echo Yii::t('app', 'Create') ?></a>
<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'tariff-grid',
    'dataProvider' => $model2->search(),
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    //'filter' => $model,
    'columns' => array(
        'title',
        'price_agent_sim',
        'price_license_fee',
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'htmlOptions' => array('style'=>'width:80px;text-align:center;vertical-align:middle'),
            'template'=>'{update} {delete}',
            'updateButtonUrl'=>'Yii::app()->controller->createUrl("tariff/update")."/id/$data->id"',
            'deleteButtonUrl'=>'Yii::app()->controller->createUrl("tariff/delete")."/id/$data->id"',
            'deleteConfirmation' => Yii::t('app','Are you sure to delete this Tariff?'),
        ),
    ),
)); ?>
