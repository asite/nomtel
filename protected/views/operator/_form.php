<div class="form">


<?php $form = $this->beginWidget('BaseTbActiveForm', array(
	'id' => 'operator-form',
    'type' => 'horizontal',
	'enableAjaxValidation' => true,
        'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false),
    	//'htmlOptions'=>array('enctype'=>'multipart/form-data')
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

            <?php echo $form->textFieldRow($model,'title',array('class'=>'span5','maxlength'=>200)); ?>
             
             <?php

  $this->widget('bootstrap.widgets.TbTabs', array(
    'type'=>'tabs', // 'tabs' or 'pills'
    'tabs'=>array(
      array('label'=>Yii::t('app', 'news'), 'content'=>$form->redactorRow($model, 'html_news', array('class'=>'span4', 'rows'=>10))),
      array('label'=>Yii::t('app', 'commands'), 'content'=>$form->redactorRow($model, 'html_commands', array('class'=>'span4', 'rows'=>10))),
      array('label'=>Yii::t('app', 'internet'), 'content'=>$form->redactorRow($model, 'html_internet', array('class'=>'span4', 'rows'=>10))),
     )
  ));

?>

        

<?php
echo '<div class="form-actions">';
echo CHtml::htmlButton('<i class="icon-ok icon-white"></i> '.Yii::t('app', 'Save'), array('class'=>'btn btn-primary', 'type'=>'submit'));
echo '&nbsp;&nbsp;&nbsp;'.CHtml::htmlButton('<i class="icon-remove"></i> '.Yii::t('app', 'Cancel'), array('class'=>'btn', 'type'=>'button', 'onclick'=>'window.location.href="'.$this->createUrl('admin').'"'));
echo '</div>';
$this->endWidget();
?>
</div>