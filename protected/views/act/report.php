<?php

$this->breadcrumbs = array(
    Act::model()->label(2)=>array('list'),
    $model->parentAct->adminLabel($model->parentAct->label(1)) => array('view','id'=>$model->id),
    $model->adminLabel($model->label(1))
);
?>

<h1><?php echo $model->adminLabel($model->label(1)); ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
    'data'=>$model,
    'attributes'=>array(
        'agent',
        'parentAct',
        'personal_account',
        'number',
        'number_price',
        'icc',
        'operator',
        'tariff'
        ),
)); ?>

<?php $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'report-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false),
    //'htmlOptions'=>array('enctype'=>'multipart/form-data')
));
?>

<?php echo $form->errorSummary($report); ?>
<?php echo $form->textAreaRow($report,'message',array('class'=>'span6','rows'=>10,'errorOptions'=>array('hideErrorMessage'=>true))); ?>

<?php
echo '<div class="form-actions">';
echo CHtml::htmlButton('<i class="icon-ok icon-white"></i> '.Yii::t('app', 'Report problem'), array('class'=>'btn btn-primary', 'type'=>'submit'));
echo '</div>';
$this->endWidget();
?>
