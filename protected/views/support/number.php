<?php

$this->breadcrumbs = array(
    Yii::t('app','Contacting Support')
);
?>

<h1><?=Yii::t('app','Contacting Support')?></h1>

<?php $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'report-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => true),
));
?>

<?php echo $form->errorSummary($report); ?>
<?php echo $form->textFieldRow($report,'number',array('class'=>'span2')); ?>
<?php echo $form->textFieldRow($report,'abonent_number',array('class'=>'span2')); ?>
<?php echo $form->textAreaRow($report,'message',array('class'=>'span6','rows'=>10,'errorOptions'=>array('hideErrorMessage'=>true))); ?>

<?php
echo '<div class="form-actions">';
echo CHtml::htmlButton('<i class="icon-ok icon-white"></i> '.Yii::t('app', 'Report problem'), array('class'=>'btn btn-primary', 'type'=>'submit'));
echo '</div>';
$this->endWidget();
?>