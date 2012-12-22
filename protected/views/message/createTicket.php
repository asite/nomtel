<?php
  $this->breadcrumbs = array(
    Yii::t('app','Create ticket'),
  );
?>

<h1><?php echo Yii::t('app','Create ticket') ?></h1>

<?php
  $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'create-ticket',
    'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false)
  ));
?>

<?php echo $form->errorSummary($model); ?>

<div class="cfix">
  <div style="float: left; margin-right: 5px;">
    <?php echo $form->textFieldRow($model,'title',array('errorOptions'=>array('hideErrorMessage'=>true),'class'=>'span4')); ?>
  </div>
</div>
<div class="cfix">
  <div style="float: left; margin-right: 5px;">
    <?php echo $form->textAreaRow($model->ticketMessages,'message',array('errorOptions'=>array('hideErrorMessage'=>true),'class'=>'span4','rows'=>5)); ?>
  </div>
</div>

<?php echo CHtml::htmlButton(Yii::t('app', 'Create'), array('class'=>'btn btn-primary', 'type'=>'submit')); ?>


<?php $this->endWidget(); ?>