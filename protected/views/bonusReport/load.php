<?php

$this->breadcrumbs = array(
    Yii::t('app', 'Load Bonuses Report'),
);

?>

<?php $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'agent-form',
    'type' => 'horizontal',
    //'enableAjaxValidation' => true,
    //'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false),
    'htmlOptions'=>array('enctype'=>'multipart/form-data')
));
?>

<?php echo $form->errorSummary($model); ?>

<div class="form-container-horizontal">
    <?php echo $form->dropDownListRow($model, 'operator', Operator::getComboList(array(''=>'')),array('class'=>'span2')); ?>
</div>

<div class="form-container-horizontal">
    <?php echo $form->FileFieldRow($model,'file',array('class'=>'span4','maxlength'=>100)); ?>
</div>

<div class="form-container-horizontal">
    <?php echo $form->textFieldRow($model,'comment',array('class'=>'span4','maxlength'=>100)); ?>
</div>

<?php
echo '<div class="form-actions">';
echo CHtml::htmlButton('<i class="icon-ok icon-white"></i> '.Yii::t('app', 'Load Bonuses Report'), array('class'=>'btn btn-primary', 'type'=>'submit'));
echo '</div>';
$this->endWidget();
?>
