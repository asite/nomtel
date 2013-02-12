<?php $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'operator-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => false,
));
?>

<style> span.error {display:none !important;} </style>
<div class="modal" style="margin-top:-150px;   ">
    <div class="modal-header" xmlns="http://www.w3.org/1999/html">
        <h3>Восстановление пароля</h3>
    </div>
    <div class="modal-body">
        <?php
        $this->widget('bootstrap.widgets.TbAlert', array(
            'block' => true, // display a larger alert block?
            'fade' => true, // use transitions?
            'closeText' => '×', // close link text - if set to false, no close link is displayed
            'alerts' => array( // configurations per alert type
                'success' => array('block' => true, 'fade' => true, 'closeText' => '×'), // success, info, warning, error or danger
                'error' => array('block' => true, 'fade' => true, 'closeText' => '×'), // success, info, warning, error or danger
            ),
        ));

        ?>
        <?=$form->errorSummary($model,'');?>
        <?=$form->maskFieldRow($model,'number','8 (999) 999-99-99')?>
    </div>
    <div class="modal-footer">
        <?php $this->widget('bootstrap.widgets.TbButton',array(
            'label'=>'Назад',
            'url'=>$this->createUrl('site/loginPO'),
            'icon'=>'chevron-left',
            'htmlOptions'=>array('style'=>'float:left;')));
        ?>
        <?php $this->widget('bootstrap.widgets.TbButton',array(
            'label'=>'Восстановить пароль',
            'buttonType'=>'submit',
            'type'=>'primary',
            'htmlOptions'=>array('style'=>'position:absolute;left:190px;'))); ?>
    </div>
</div>

<?php $this->endWidget(); ?>

