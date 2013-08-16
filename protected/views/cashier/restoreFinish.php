
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

<h4><?php echo $megafonAppRestoreNumber->cashier_debit_credit_id ? ' Абонент произвел оплату восстановления при ее заказе ':
        'Абонент выбрал оплату при получении SIM'?></h4>

<?php $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'restore-finish-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => false,
    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false),
));
?>

<?php if (!$megafonAppRestoreNumber->cashier_debit_credit_id) { ?>

<?=$form->errorSummary($model); ?>

<p class="note">
    <?=Yii::t('app', 'Fields with')?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
</p>

<?=$form->textFieldRow($model,'sum',array('class'=>'span2'))?>

<?php } ?>
<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton',array(
        'buttonType'=>'submit',
        'label'=>'Завершить восстановление'
    ));?>
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
