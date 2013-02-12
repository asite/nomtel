<?php $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'operator-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => false,
));
?>

<style> span.error {display:none !important;} </style>
    <div class="modal" style="margin-top:-150px;   ">
        <div class="modal-header" xmlns="http://www.w3.org/1999/html">
            <h3><?php echo Yii::t('app','Please login');?></h3>
        </div>
        <div class="modal-body">
                    <?=$form->errorSummary($model,'');?>

                    <?=$form->maskFieldRow($model,'number','8 (999) 999-99-99')?>
                    <?=$form->passwordFieldRow($model,'password')?>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" onclick="$('form').submit()" style="position:absolute;left:195px;">
                <?php echo Yii::t('app','Enter')?> <i class="icon-chevron-right icon-white"></i>
            </button>
            <?php $this->widget('bootstrap.widgets.TbButton',array('label'=>'Восстановить пароль','type'=>'url','url'=>$this->createUrl('pOSite/restorePassword'))); ?>
        </div>
    </div>

<?php $this->endWidget(); ?>

