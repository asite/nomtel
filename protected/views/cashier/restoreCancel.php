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

<h4><?=$message?></h4>

<?php $this->widget('bootstrap.widgets.TbButton',array(
    'url'=>$this->createUrl('restoreDoCancel',array('id'=>$number->id)),
    'label'=>$megafonAppRestoreNumber->cashier_debit_credit_id ? ' Отклонить восстановление и вернуть клиенту '.$megafonAppRestoreNumber->cashierDebitCredit->sum.' р.':
        'Отклонить восстановление (клиент выбрал оплату при получении SIM)',
    'htmlOptions'=>array('onclick'=>'return confirm("Отклонить восстановление?")')
));
?>
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
