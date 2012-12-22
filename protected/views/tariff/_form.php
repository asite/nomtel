<div class="form">


<?php $form = $this->beginWidget('BaseTbActiveForm', array(
	'id' => 'tariff-form',
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

            <?php echo $form->hiddenField($model,'operator_id'); ?>
  <div class="form-label-width-200">
                <?php echo $form->textFieldRow($model,'title',array('class'=>'span5','maxlength'=>50)); ?>

                <?php echo $form->textFieldRow($model,'price_agent_sim',array('class'=>'span1')); ?>

                <?php echo $form->textFieldRow($model,'price_license_fee',array('class'=>'span1')); ?>
  </div>
        

<?php
echo '<div class="form-actions">';
echo CHtml::htmlButton('<i class="icon-ok icon-white"></i> '.Yii::t('app', 'Save'), array('class'=>'btn btn-primary', 'type'=>'submit'));
echo '&nbsp;&nbsp;&nbsp;'.CHtml::htmlButton('<i class="icon-remove"></i> '.Yii::t('app', 'Cancel'), array('class'=>'btn', 'type'=>'button', 'onclick'=>"window.location.href='".$this->createUrl('operator/update',array('id'=>$model->operator_id))."'"));
echo '</div>';
$this->endWidget();
?>
</div>