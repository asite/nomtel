<?php

$this->breadcrumbs = array(
    Yii::t('app', 'Обработка заявления'),
);

?>

<h1>Обработка заявления</h1>


<?php $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'restore-process-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => false,
    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false),
));
?>

<p class="note">
    <?=Yii::t('app', 'Fields with')?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
</p>

<?=$form->textFieldRow($processModel,'number',array('class'=>'span2'))?>

<?=$form->textFieldRow($processModel,'icc',array('class'=>'span2'))?>

<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton',array(
        'buttonType'=>'submit',
        'label'=>'Восстановить'
    ));?>
</div>

<?php $this->endWidget(); ?>


<?php $this->widget('TbExtendedGridViewExport', array(
    'id' => 'sim-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter' => $model,
    'columns' => array(
        array(
            'name'=>'number',
            'header'=>Yii::t('app','Number'),
            'htmlOptions' => array('style'=>'text-align:center;'),
        ),
        array(
            'name'=>'dt',
            'header'=>'Дата заявления',
            'value'=>'new EDateTime($data["dt"],null,"date")',
            'htmlOptions' => array('style'=>'text-align:center;'),
        ),
        array(
            'name'=>'id',
            'header'=>'Номер заявления',
            'htmlOptions' => array('style'=>'text-align:center;'),
        ),
    ),
)); ?>

