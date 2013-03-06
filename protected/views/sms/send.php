<h1><?php echo Yii::t('app','Send Sms'); ?></h1>

<?php
  $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'send-sms',
    'type' => 'horizontal',
    'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false)
  ));
?>

<?=$form->maskFieldRow($model,'number','8 (999) 999-99-99');?>
<?=$form->textareaRow($model,'text',array('class'=>'span4','rows'=>5));?>

<?php echo CHtml::htmlButton(Yii::t('app', 'moveSim'), array('class'=>'btn','style'=>'margin-left: 180px', 'type'=>'submit')); ?>

<?php $this->endWidget(); ?>