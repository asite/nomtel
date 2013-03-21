<h1><?=Yii::t('app','addBlankSim')?></h1>

<?php
$form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'add-blank-sim',
    'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false)
));
?>


<?php echo $form->errorSummary($model); ?>

<br/>
<div class="cfix">
    <div class="cfix">
        <div style="float: left; margin-right: 5px;">
            <?php echo $form->dropDownListRow($model, 'type', BlankSim::getTypeDropDownList(array(''=>'Выберите тип'))); ?>
        </div>
        <div style="float: left; margin-right: 5px;">
            <?php
            $opListArray=Operator::getComboList(array(''=>'Выберите оператора'));
            echo $form->dropDownListRow($model, 'operator_id', $opListArray); ?>
        </div>
    </div>
    <?php echo $form->textAreaRow($model, 'icc', array('rows'=>20)); ?>
</div>

<?php echo CHtml::htmlButton(Yii::t('app', 'addBlankSim'), array('class'=>'btn btn-primary', 'name'=>'buttonAddSim', 'type'=>'submit')); ?>

<?php $this->endWidget(); ?>
