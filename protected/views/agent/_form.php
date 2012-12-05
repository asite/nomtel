<div class="form">


<?php $form = $this->beginWidget('BaseTbActiveForm', array(
	'id' => 'agent-form',
    'type' => 'horizontal',
	'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false),
    //'htmlOptions'=>array('enctype'=>'multipart/form-data')
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

    <?php echo $form->errorSummary(array($user,$model)); ?>
<fieldset><legend><?php echo Yii::t('app','Authorization data');?></legend>
    <div class="form-container-horizontal">
        <div class="form-container-item form-label-width-140">
            <?php echo $form->textFieldRow($user,'username',array('autocomplete'=>'off','class'=>'span3','maxlength'=>100,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
        </div>
        <div class="form-container-item form-label-width-100">
            <?php echo $form->passwordFieldRow($user,'password',array('autocomplete'=>'off','class'=>'span3','maxlength'=>100,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
        </div>
    </div>
</fieldset>
    <fieldset><legend><?php echo Yii::t('app','Agent');?></legend>
<div class="form-container-horizontal">
<div class="form-container-item form-label-width-140">
                <?php echo $form->textFieldRow($model,'name',array('class'=>'span3','maxlength'=>100,'errorOptions'=>array('hideErrorMessage'=>true))); ?>

                <?php echo $form->textFieldRow($model,'surname',array('class'=>'span3','maxlength'=>100,'errorOptions'=>array('hideErrorMessage'=>true))); ?>

                <?php echo $form->textFieldRow($model,'middle_name',array('class'=>'span3','maxlength'=>100,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
</div>
<div class="form-container-item form-label-width-100">
                <?php echo $form->textFieldRow($model,'phone_1',array('class'=>'span3','maxlength'=>50,'errorOptions'=>array('hideErrorMessage'=>true))); ?>

                <?php echo $form->textFieldRow($model,'phone_2',array('class'=>'span3','maxlength'=>50,'errorOptions'=>array('hideErrorMessage'=>true))); ?>

                <?php echo $form->textFieldRow($model,'phone_3',array('class'=>'span3','maxlength'=>50,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
</div>
<div class="form-container-item form-label-width-80">
                <?php echo $form->textFieldRow($model,'email',array('class'=>'span3','maxlength'=>100,'errorOptions'=>array('hideErrorMessage'=>true))); ?>

                <?php echo $form->textFieldRow($model,'skype',array('class'=>'span3','maxlength'=>100,'errorOptions'=>array('hideErrorMessage'=>true))); ?>

                <?php echo $form->textFieldRow($model,'icq',array('class'=>'span3','maxlength'=>20,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
</div>
</div>
    </fieldset>
    <fieldset><legend><?php echo Yii::t('app','Passport');?></legend>
        <div class="form-container-horizontal">
            <div class="form-container-item form-label-width-140">
                <?php echo $form->textFieldRow($model,'passport_series',array('class'=>'span1','maxlength'=>10,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
            </div>
            <div class="form-container-item form-label-width-80">
                <?php echo $form->textFieldRow($model,'passport_number',array('class'=>'span2','maxlength'=>20,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
            </div>
            <div class="form-container-item form-label-width-120">
                <?php echo $form->pickerDateRow($model,'passport_issue_date',array('class'=>'span2','errorOptions'=>array('hideErrorMessage'=>true))); ?>
            </div>
        </div>

        <div class="form-container-horizontal">
            <div class="form-container-item form-label-width-140">
                <?php echo $form->textFieldRow($model,'passport_issuer',array('class'=>'span8','maxlength'=>200,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
            </div>
        </div>

        <div class="form-container-horizontal">
            <div class="form-container-item form-label-width-140">
                <?php echo $form->pickerDateRow($model,'birthday_date',array('class'=>'span2','errorOptions'=>array('hideErrorMessage'=>true))); ?>
            </div>
            <div class="form-container-item form-label-width-140">
                <?php echo $form->textFieldRow($model,'birthday_place',array('class'=>'span3','maxlength'=>200,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
            </div>
        </div>

        <div class="form-container-horizontal">
            <div class="form-container-item form-label-width-140">
                <?php echo $form->textFieldRow($model,'registration_address',array('class'=>'span8','maxlength'=>200,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
            </div>
        </div>
    </fieldset>
        

<?php
echo '<div class="form-actions">';
echo CHtml::htmlButton('<i class="icon-ok icon-white"></i> '.Yii::t('app', 'Save'), array('class'=>'btn btn-primary', 'type'=>'submit'));
echo '&nbsp;&nbsp;&nbsp;'.CHtml::htmlButton('<i class="icon-remove"></i> '.Yii::t('app', 'Cancel'), array('class'=>'btn', 'type'=>'button', 'onclick'=>'window.location.href="'.$this->createUrl('admin').'"'));
echo '</div>';
$this->endWidget();
?>
</div>