<div class="form">


    <?php $form = $this->beginWidget('BaseTbActiveForm', array(
    	'id' => 'support-operator-form',
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
        <fieldset>
            <legend><?php echo Yii::t('app','Authorization data');?></legend>
            <div class="form-container-horizontal">
                <div class="form-container-item form-label-width-140">
                    <?php echo $form->textFieldRow($user,'username',array('autocomplete'=>'off','class'=>'span2','maxlength'=>100,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
                </div>
                <div class="form-container-item form-label-width-100">
                    <?php echo $form->passwordFieldRow($user,'password',array('autocomplete'=>'off','class'=>'span2`','maxlength'=>100,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
                </div>
            </div>
        </fieldset>
        <fieldset><legend><?php echo Operator::label(1);?></legend>

    	<?php //echo $form->errorSummary($model); ?>

        <?php //echo $form->textFieldRow($model,'user_id',array('class'=>'span5')); ?>
        <?php echo $form->textFieldRow($model,'name',array('class'=>'span5','maxlength'=>200,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
        <?php echo $form->textFieldRow($model,'surname',array('class'=>'span5','maxlength'=>200,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
        <?php echo $form->textFieldRow($model,'middle_name',array('class'=>'span5','maxlength'=>200,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
        <?php echo $form->textFieldRow($model,'phone',array('class'=>'span5','maxlength'=>200,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
            <?php echo $form->textFieldRow($model,'email',array('class'=>'span5','maxlength'=>200,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
        <?php
        echo '<div class="form-actions">';
        echo CHtml::htmlButton('<i class="icon-ok icon-white"></i> '.Yii::t('app', 'Save'), array('class'=>'btn btn-primary', 'type'=>'submit'));
        echo '&nbsp;&nbsp;&nbsp;'.CHtml::htmlButton('<i class="icon-remove"></i> '.Yii::t('app', 'Cancel'), array('class'=>'btn', 'type'=>'button', 'onclick'=>'window.location.href=\''.$this->createUrl('admin').'\''));
        echo '</div>';
    $this->endWidget();
    ?>
</div>