<style>
    .form-horizontal .controls .help-inline {display:none;}
</style>

	<input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken;?>">
    <div class="modal" style="top:50%;margin-top:-150px;">

            <div class="modal-header" xmlns="http://www.w3.org/1999/html">
                <h3>Пожалуйста введите ваш новый пароль</h3>
            </div>

            <div class="modal-body">
                <?php $form = $this->beginWidget('BaseTbActiveForm', array(
                    'id' => 'password-form',
                    'type' => 'horizontal',
                ));
                ?>

                <?=$form->errorSummary($model)?>
                <?php echo $form->passwordFieldRow($model,'password',array('autocomplete'=>'off','class'=>'span2`','maxlength'=>100,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
                <?php echo $form->passwordFieldRow($model,'password2',array('autocomplete'=>'off','class'=>'span2`','maxlength'=>100,'errorOptions'=>array('hideErrorMessage'=>true))); ?>


                <?php $this->endWidget();?>
            </div>

        <div class="modal-footer">
            <button class="btn btn-primary" onclick="$('form').submit()">
                <?php echo Yii::t('app','Save')?>
            </button>
        </div>

</div>
