<h1>Статистика по кассе</h1>

<div class="cfix">
    <?php $form=$this->beginWidget('BaseTbActiveForm',array(
    'id'=>'filter',
    'type'=>'horizontal'
)); ?>

    <div class="form-container-horizontal">
        <div class="form-container-item form-label-width-60">
            <?php echo $form->PickerDateRow($model,'date_from',array('style'=>'width:80px;','errorOptions'=>array('hideErrorMessage'=>true)),array('minYearDelta'=>5,'maxYearDelta'=>0,'onSelect'=>'js:function(){$(this).closest("form").submit();}')); ?>
        </div>
        <?php if (Yii::app()->user->role=='supportSuper') { ?>
        <div class="form-container-item form-label-width-80">
            <?php echo $form->dropDownListRow($model,'support_operator_id',SupportOperator::getCashierComboList(array(''=>'Общий баланс')),array('class'=>'span2','errorOptions'=>array('hideErrorMessage'=>true))); ?>
        </div>
        <?php } ?>
    </div>
<?php /*
    <div class="form-container-horizontal">
        <div class="form-container-item form-label-width-60">
            <?php echo $form->maskFieldRow($model,'date_to','99.99.9999',array('style'=>'width:80px;','errorOptions'=>array('hideErrorMessage'=>true))); ?>
        </div>
    </div>
    <div class="form-container-horizontal">&nbsp;
        <?php $this->widget("bootstrap.widgets.TbButton",array(
            'url'=>$this->createUrl('',array('CashierStatistic[date_from]'=>new EDateTime(),'CashierStatistic[date_to]'=>new EDateTime())),
            'label'=>'Сегодня'
        ));?>
        <?php
        $date_from=new EDateTime();
        $date_from->modify("-1 DAY");
        $this->widget("bootstrap.widgets.TbButton",array(
            'url'=>$this->createUrl('',array('CashierStatistic[date_from]'=>$date_from,'CashierStatistic[date_to]'=>$date_from)),
            'label'=>'Вчера'
        ));?>
        <?php
        $date_from=new EDateTime();
        $date_from->modify("-7 DAY");
        $this->widget("bootstrap.widgets.TbButton",array(
        'url'=>$this->createUrl('',array('CashierStatistic[date_from]'=>$date_from,'CashierStatistic[date_to]'=>new EDateTime())),
        'label'=>'Неделя'
        ));?>
        <?php
        $date_from=new EDateTime();
        $date_from->modify("-1 MONTH");
        $this->widget("bootstrap.widgets.TbButton",array(
            'url'=>$this->createUrl('',array('CashierStatistic[date_from]'=>$date_from,'CashierStatistic[date_to]'=>new EDateTime())),
            'label'=>'Месяц'
        ));?>
    </div>
*/ ?>
    <?php $this->endWidget(); ?>
</div>

<script>
    $('#filter').on('keydown',function(event){
        if (event.type === 'keydown' && event.keyCode == 13) $('#filter').submit();
    });

    $('#filter select').on('change',function(event){
        $('#filter').submit();
    });
</script>

<h2>Текущий баланс: <?=$balance?></h2>

<h2>Баланс утро: <?=$morningBalance?></h2>
<?php $this->widget('TbExtendedGridViewExport', array(
    'id' => 'summary-stats-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'columns' => array(
        array(
            'name'=>'support_operator',
            'header'=>'Кассир',
            'value'=>'$data["surname"]." ".$data["name"]',
            'visible'=>!$model->support_operator_id
        ),
        array(
            'name'=>'cnt_sell',
            'header'=>'Кол-во подтвержденных продаж',
            'htmlOptions'=>array('style'=>'text-align:center;')
        ),
        array(
            'name'=>'cnt_restore',
            'header'=>'Кол-во подтвержденных восстановлений',
            'htmlOptions'=>array('style'=>'text-align:center;')
        ),
        array(
            'name'=>'sum',
            'header'=>'Сумма в кассу',
            'htmlOptions'=>array('style'=>'text-align:center;')
        ),
        array(
            'name'=>'sum_cashier',
            'header'=>'Вознаграждение',
            'htmlOptions'=>array('style'=>'text-align:center;')
        ),
    ),
)); ?>
<?php if (!$model->support_operator_id) { ?>
<b>Сумма в кассу, включая неподтвержденные операции:</b> <?=$total?>
<?php } ?>

<h2>Продажи</h2>
<?php $this->widget('TbExtendedGridViewExport', array(
    'id' => 'sell-grid',
    'dataProvider' => $cashierNumberSellDataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter'=>$cashierNumberModel,
    'columns' => array(
        array(
            'name'=>'support_operator_id',
            'header'=>'Кассир',
            'value'=>'$data["surname"]." ".$data["name"]',
            'filter'=>SupportOperator::getCashierComboList(),
            'visible'=>!$model->support_operator_id
        ),
        array(
            'name'=>'number',
            'header'=>'Номер',
            'htmlOptions'=>array('style'=>'text-align:center'),
        ),
        array(
            'name'=>'confirmed',
            'header'=>'Подтверждено',
            'value'=>'$data["confirmed"] ? "Да":"Нет"',
            'htmlOptions'=>array('style'=>'text-align:center'),
            'filter'=>array('0'=>'Нет','1'=>'Да')
        ),
        array(
            'name'=>'sum',
            'header'=>'Сумма в кассу',
            'filter'=>false,
            'htmlOptions'=>array('style'=>'text-align:center'),
        ),
    ),
)); ?>

<h2>Восстановления</h2>
<?php $this->widget('TbExtendedGridViewExport', array(
    'id' => 'restore-grid',
    'dataProvider' => $cashierNumberRestoreDataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter'=>$cashierNumberModel,
    'columns' => array(
        array(
            'name'=>'support_operator_id',
            'header'=>'Кассир',
            'value'=>'$data["surname"]." ".$data["name"]',
            'filter'=>SupportOperator::getCashierComboList(),
            'visible'=>!$model->support_operator_id
        ),
        array(
            'name'=>'number',
            'header'=>'Номер',
            'htmlOptions'=>array('style'=>'text-align:center'),
        ),
        array(
            'name'=>'confirmed',
            'header'=>'Подтверждено',
            'value'=>'$data["confirmed"] ? "Да":"Нет"',
            'htmlOptions'=>array('style'=>'text-align:center'),
            'filter'=>array('0'=>'Нет','1'=>'Да')
        ),
        array(
            'name'=>'sum',
            'header'=>'Сумма в кассу',
            'filter'=>false,
            'htmlOptions'=>array('style'=>'text-align:center'),
        ),
        array(
            'name'=>'sum_cashier',
            'header'=>'Вознаграждение кассира',
            'filter'=>false,
            'htmlOptions'=>array('style'=>'text-align:center'),
        ),
    ),
)); ?>

<h2>Инкассации
    <?php if (Yii::app()->user->role=='cashier') { ?>
    <?php $this->widget("bootstrap.widgets.TbButton",array(
        'url'=>$this->createUrl('collectionStep1',array('cashier_support_operator_id'=>loggedSupportOperatorId())),
        'label'=>'Инкассация'
    ));?>
    <?php } ?>
</h2>
<?php $this->widget('TbExtendedGridViewExport', array(
        'id' => 'collection-grid',
        'dataProvider' => $collectionDataProvider,
        'itemsCssClass' => 'table table-striped table-bordered table-condensed',
        'columns' => array(
            array(
                'name'=>'cashier_support_operator_id',
                'header'=>'Кассир',
                'value'=>'$data["surname"]." ".$data["name"]',
                'filter'=>SupportOperator::getCashierComboList(),
                'visible'=>!$model->support_operator_id
            ),
            array(
                'name'=>'dt',
                'header'=>'Дата',
                'value'=>'date("d.m.Y H:i:s",strtotime($data->dt))',
            ),
            array(
                'name'=>'collector_support_operator_id',
                'header'=>'Инкассатор',
                'value'=>'$data->collectorSupportOperator',
            ),
            array(
                'name'=>'sum',
                'header'=>'Сумма',
                'htmlOptions'=>array('style'=>'text-align:center'),
            ),
        ),
    )); ?>

<h2>Баланс вечер: <?=$eveningBalance?></h2>
