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

<?php
  $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'add-message',
    'method'=>'post',
    'action'=>$this->createUrl('answer',array('ticket'=>$_GET['id'])),
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
<br/>

<?php echo CHtml::htmlButton(Yii::t('app', 'Answer'), array('class'=>'btn btn-primary', 'type'=>'submit')); ?>

<?php $this->endWidget(); ?>