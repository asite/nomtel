<h1>Массовое восстановление</h1>

<?php $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'bulk-restore-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false),
));
?>

<?=$form->textAreaRow($model,'data',array('class'=>'span4','rows'=>20));?>

<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton',array(
        'buttonType'=>'submit',
        'label'=>'Восстановить на продажу',
        'htmlOptions'=>array('name'=>'action','value'=>'restoreToBase')
    ));?>
    <?php $this->widget('bootstrap.widgets.TbButton',array(
        'buttonType'=>'submit',
        'label'=>'Восстановить на продажу',
        'htmlOptions'=>array('name'=>'action','value'=>'restoreToAgent')
    ));?>
    <?php $this->widget('bootstrap.widgets.TbButton',array(
        'buttonType'=>'submit',
        'label'=>'Восстановить на продажу',
        'htmlOptions'=>array('name'=>'action','value'=>'restoreToNumber')
    ));?>
</div>


<?php $this->endWidget(); ?>

