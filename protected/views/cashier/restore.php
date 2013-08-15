<h1>Восстановление номера '<?=$number->number?>'</h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
    'data'=>$sim,
    'attributes'=>array(
        array(
            'label'=>Yii::t('app','Tariff')."<br/><br/>",
            'value'=>$sim->tariff
        ),
    ),
)); ?>

<script>
    function toggleSum() {
        if (jQuery('input[name="CashierNumberRestore1[payment]"][value=IMMEDIATE]').is(':checked'))
            jQuery('#CashierNumberRestore1_sum').closest('.control-group').show();
        else
            jQuery('#CashierNumberRestore1_sum').closest('.control-group').hide();
    }

    jQuery(toggleSum);
</script>
<div class="form">

<?php $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'restore-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => false,
    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false),
));
?>

    <p class="note">
        <?=Yii::t('app', 'Fields with')?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
    </p>

    <?=$form->errorSummary($model); ?>

    <?=$form->radioButtonListRow($model,'sim_type',MegafonAppRestoreNumber::getSimTypeLabels())?>

    <?=$form->textFieldRow($model,'contact_phone',array('class'=>'span2'))?>

    <?=$form->textFieldRow($model,'contact_name',array('class'=>'span2'))?>

    <?=$form->radioButtonListRow($model,'payment',$model::getPaymentLabels(),array('onchange'=>'toggleSum()'))?>

    <?=$form->textFieldRow($model,'sum',array('class'=>'span2'))?>

    <div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton',array(
        'buttonType'=>'submit',
        'label'=>'Восстановить'
    ));?>
    </div>
</div>

<?php $this->endWidget(); ?>

<h2>История баланса</h2>
<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'balances-grid',
    'dataProvider' => $balancesDataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'columns' => array(
        array(
            'header'=>'Дата',
            'value'=>'new EDateTime($data["dt"],null,"date")'
        ),
        array(
            'name'=>'balance',
            'header'=>'Баланс',
        ),
    ),
)); ?>
