<?php
  $this->breadcrumbs = array(
    Yii::t('app','Create message'),
  );
?>

<h1><?php echo Yii::t('app','Create message') ?></h1>

<?php
  $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'create-message',
    'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false)
  ));
?>

<?php echo $form->errorSummary($model); ?>
<?php echo $form->errorSummary($model->ticketMessages); ?>

<div style="display: none;"><?php echo $form->error($model,'whom'); ?></div>

<div class="cfix">
  <div style="float: left; margin-right: 5px;">
    <?php echo $form->textAreaRow($model->ticketMessages,'message',array('errorOptions'=>array('hideErrorMessage'=>true),'class'=>'span4','rows'=>5)); ?>
  </div>
</div>
<label class="control-label" for="Ticket_whom"><?php echo Yii::t('app','Agent'); ?> <span class="required">*</span></label>
<div class="controls">
  <?php
    $this->widget('bootstrap.widgets.TbSelect2', array(
      'name' => 'Ticket[whom]',
      'data' => $agent,
      'options' => array(
        'width' => '366px',
      )
    ));
  ?>
</div>
<br/>

<?php echo CHtml::htmlButton(Yii::t('app', 'Send'), array('class'=>'btn btn-primary', 'type'=>'submit')); ?>


<?php $this->endWidget(); ?>