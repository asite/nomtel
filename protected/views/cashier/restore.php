<h1>Восстановление номера '<?=$number->number?>'</h1>

<div class="form">

<?php $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'operator-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => false,
    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false),
));
?>

    <p class="note">
        <?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
    </p>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->textFieldRow($model,'icc',array('class'=>'span3','maxlength'=>25,'hint'=>'введите icc с регионом "'.$number->sim->operatorRegion->title.'"')); ?>

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
