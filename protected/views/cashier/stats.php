<h1>Статистика по кассе</h1>

<div class="cfix">
    <?php $form=$this->beginWidget('BaseTbActiveForm',array(
    'id'=>'filter',
    'type'=>'horizontal'
)); ?>

    <div class="form-container-horizontal">
        <div class="form-container-item form-label-width-60">
            <?php echo $form->maskFieldRow($model,'date_from','99.99.9999',array('class'=>'span2','errorOptions'=>array('hideErrorMessage'=>true))); ?>
        </div>
    </div>

    <div class="form-container-horizontal">
        <div class="form-container-item form-label-width-60">
            <?php echo $form->maskFieldRow($model,'date_to','99.99.9999',array('class'=>'span2','errorOptions'=>array('hideErrorMessage'=>true))); ?>
        </div>
    </div>
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

<h2>Итоги</h2>
<?php $this->widget('TbExtendedGridViewExport', array(
    'id' => 'summary-stats-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'columns' => array(
        array(
            'name'=>'support_operator',
            'header'=>'Кассир',
            'value'=>'$data["surname"]." ".$data["name"]'
        ),
        array(
            'name'=>'cnt_sell',
            'header'=>'Кол-во продаж',
            'htmlOptions'=>array('style'=>'text-align:center;')
        ),
        array(
            'name'=>'cnt_restore',
            'header'=>'Кол-во восстановлений',
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

<h2>Продажи</h2>
<?php $this->widget('TbExtendedGridViewExport', array(
    'id' => 'stats-grid',
    'dataProvider' => $cashierNumberSellDataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter'=>$cashierNumberSellModel,
    'columns' => array(
        array(
            'header'=>'Кассир',
            'value'=>'$data->supportOperator'
        ),
        array(
            'header'=>'Номер',
            'value'=>'$data->number->number'
        ),
        array(
            'name'=>'confirmed',
        ),
        array(
            'name'=>'sum',
        ),
        array(
            'name'=>'sum_cashier',
        ),
    ),
)); ?>

<h2>Восстановления</h2>
<?php $this->widget('TbExtendedGridViewExport', array(
    'id' => 'stats-grid',
    'dataProvider' => $cashierNumberRestoreDataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter'=>$cashierNumberRestoreModel,
    'columns' => array(
        array(
            'header'=>'Кассир',
            'value'=>'$data->supportOperator'
        ),
        array(
            'header'=>'Номер',
            'value'=>'$data->number->number'
        ),
        array(
            'name'=>'confirmed',
        ),
        array(
            'name'=>'sum',
        ),
        array(
            'name'=>'sum_cashier',
        ),
    ),
)); ?>
