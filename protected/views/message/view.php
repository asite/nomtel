<?php
  $this->breadcrumbs = array(
    Yii::t('app','View message'),
  );
?>

<h1><?php echo Yii::t('app','View message') ?></h1>


<div class="message_views">
<?php
  $agent = loggedAgentId();

  foreach($messages as $v)
    $this->widget('bootstrap.widgets.TbBox', array(
      'title' => $agents[$v->agent_id].' - '.$v->dt,
      'headerIcon' => 'icon-user',
      'content' => $v->message
    ));
?>
</div>

<?php if (!isset($params['tiketClosed'])) { ?>
  <?php
    $form = $this->beginWidget('BaseTbActiveForm', array(
      'id' => 'add-message',
      'method'=>'post',
      'action'=>$this->createUrl('answer',array('ticket'=>$_GET['id'], 'type'=>$_GET['type'])),
      'enableAjaxValidation' => true,
      'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false)
    ));
  ?>


  <?php echo $form->errorSummary($model); ?>
  <div class="cfix">
    <div style="float: left; margin-right: 5px;">
      <?php echo $form->textAreaRow($model,'message',array('errorOptions'=>array('hideErrorMessage'=>true),'class'=>'span4','rows'=>5)); ?>
    </div>
  </div>
  <?php echo CHtml::htmlButton(Yii::t('app', 'Answer'), array('class'=>'btn btn-primary', 'type'=>'submit')); ?>

  <?php $this->endWidget(); ?>

  <?php if (isset($params['closeMessage'])) { ?>
    <?php $form = $this->beginWidget('BaseTbActiveForm', array(
      'id' => 'close-ticket',
      'method'=>'post',
      'action'=>$this->createUrl('close',array('ticket'=>$_GET['id'], 'type'=>$_GET['type'])),
      'enableAjaxValidation' => false,
      'clientOptions'=>array('validateOnSubmit' => false, 'validateOnChange' => false)
    )); ?>
    <br/><br/>
    <div class="cfix">
      <?php if (isset($params['priseMessage'])) { ?>
        <label style="float: left; line-height: 30px;" for="PriseMessage_prise" class="required"><?php echo Yii::t('app','Enter prise'); ?>:</label>
        <input style="float: left; margin: 0 20px 0 5px;" name="PriseMessage[prise]" id="PriseMessage_prise" type="text" value="0">
      <?php } ?>
      <?php echo CHtml::htmlButton(Yii::t('app', 'Close ticket'), array('class'=>'btn btn-primary', 'type'=>'submit', 'style'=>'float: left;')); ?>
    </div>
    <?php $this->endWidget(); ?>

  <?php } ?>
<?php } else { echo '<h2>'.Yii::t('app','Ticket closed').'</h2>'; } ?>