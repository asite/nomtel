<div class="form">


<?php $form = $this->beginWidget('BaseTbActiveForm', array(
	'id' => 'blank-sim-form',
    'type' => 'horizontal',
	'enableAjaxValidation' => false,
    	//'htmlOptions'=>array('enctype'=>'multipart/form-data')
));
?>
    <script>
        function changeOperator(mode) {
            $.ajax({
                type: "POST",
                url: "<?php echo $this->createUrl('ajaxOperatorCombo') ?>",
                data: { YII_CSRF_TOKEN: $('[name="YII_CSRF_TOKEN"]').val(), operatorId: $(mode).val() }
            }).done(function( msg ) {
                        $(mode).closest('form').find('select[name*="[operator_region_id]"]').html(msg);
                    });
        }
    </script>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

            <?php echo $form->dropDownListRow($model,'type',BlankSim::getTypeDropDownList(),array('class'=>'span3','maxlength'=>6)); ?>

                <?php echo $form->textFieldRow($model,'icc',array('class'=>'span3','maxlength'=>50)); ?>

                <?php echo $form->dropDownListRow($model,'operator_id',Operator::getComboList(),array('onchange'=>'changeOperator(this);','class'=>'span3')); ?>

                <?php $regions=OperatorRegion::getDropDownList(); echo $form->dropDownListRow($model,'operator_region_id',$regions[$model->operator_id],array('class'=>'span3')); ?>


<?php
echo '<div class="form-actions">';
echo CHtml::htmlButton('<i class="icon-ok icon-white"></i> '.Yii::t('app', 'Save'), array('class'=>'btn btn-primary', 'type'=>'submit'));
echo '&nbsp;&nbsp;&nbsp;'.CHtml::htmlButton('<i class="icon-remove"></i> '.Yii::t('app', 'Cancel'), array('class'=>'btn', 'type'=>'button', 'onclick'=>'window.location.href=\''.$this->createUrl('admin').'\''));
echo '</div>';
$this->endWidget();
?>
</div>