<h1><?=Yii::t('app','addBlankSim')?></h1>

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
            echo $form->dropDownListRow($model, 'operator_id', $opListArray,array('onchange'=>'changeOperator(this);')); ?>

            <?php $regions=OperatorRegion::getDropDownList();
            $key=$model->operator_id ? $model->operator:key($opListArray);
            if (!isset($regions[$key])) $regions[$key]=array('' => 'Выбор региона');
            ?>
        </div>
        <div style="float: left; margin-right: 5px;">
            <?php echo $form->dropDownListRow($model, 'operator_region_id', $regions[$key]); ?>
        </div>
    </div>
    <?php echo $form->textAreaRow($model, 'icc', array('rows'=>20)); ?>
</div>

<?php echo CHtml::htmlButton(Yii::t('app', 'addBlankSim'), array('class'=>'btn btn-primary', 'name'=>'buttonAddSim', 'type'=>'submit')); ?>

<?php $this->endWidget(); ?>
