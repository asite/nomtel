<?php

$this->breadcrumbs = array(
    Number::label(2),
);

?>

<h1><?php echo Yii::t('app','Set Number Region'); ?></h1>

<?php
  $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'set-number-region',
    'type' => 'horizontal',
    'enableAjaxValidation' => false,
    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false)
  ));
?>

<?php echo CHtml::htmlButton(Yii::t('app', 'Set Region'), array('name'=>'setNumberRegion', 'class'=>'btn', 'type'=>'submit')); ?>

<?php $this->endWidget(); ?>