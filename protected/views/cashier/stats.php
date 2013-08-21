<h1>Баланс кассы</h1>

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
    'dataProvider' => $summary,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'columns' => array(
        array(
            'name'=>'sells',
            'header'=>'Кол-во продаж',
            'htmlOptions'=>array('style'=>'text-align:center;')
        ),
        array(
            'name'=>'restores',
            'header'=>'Кол-во восстановлений',
            'htmlOptions'=>array('style'=>'text-align:center;')
        ),
        array(
            'name'=>'sum',
            'header'=>'Сумма сделок',
            'htmlOptions'=>array('style'=>'text-align:center;')
        ),
    ),
)); ?>

<h2>Продажи</h2>
<?php $this->widget('TbExtendedGridViewExport', array(
    'id' => 'sell-grid',
    'dataProvider' => $cashierNumberSellDataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter'=>$cashierSellNumberModel,
    'columns' => array(
        array(
            'name'=>'type',
            'header'=>'Тип',
            'htmlOptions'=>array('style'=>'text-align:center'),
            'value'=>'CashierSellNumber::getTypeLabel($data["type"])',
            'filter'=>CashierSellNumber::getTypeDropDownList()
        ),
        array(
            'name'=>'number',
            'header'=>'Номер',
            'htmlOptions'=>array('style'=>'text-align:center'),
        ),
        array(
            'name'=>'sum',
            'header'=>'Сумма сделки',
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
    'filter'=>$cashierRestoreNumberModel,
    'columns' => array(
        array(
            'name'=>'number',
            'header'=>'Номер',
            'htmlOptions'=>array('style'=>'text-align:center'),
        ),
        array(
            'name'=>'sum',
            'header'=>'Сумма сделки',
            'filter'=>false,
            'htmlOptions'=>array('style'=>'text-align:center'),
        ),
    ),
)); ?>

<h2>Другое</h2>
<?php $this->widget('TbExtendedGridViewExport', array(
    'id' => 'other-grid',
    'dataProvider' => $otherDataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter'=>$otherDataModel,
    'columns' => array(
        'comment',
        array(
            'name'=>'sum',
            'header'=>'Сумма сделки',
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
