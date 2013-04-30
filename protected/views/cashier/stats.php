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
            'value'=>'$data["surname"]." ".$data["name"]',
            'visible'=>Yii::app()->user->role!='cashier'
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
            'visible'=>Yii::app()->user->role!='cashier'
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
    'id' => 'sell-grid',
    'dataProvider' => $cashierNumberRestoreDataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter'=>$cashierNumberModel,
    'columns' => array(
        array(
            'name'=>'support_operator_id',
            'header'=>'Кассир',
            'value'=>'$data["surname"]." ".$data["name"]',
            'filter'=>SupportOperator::getCashierComboList(),
            'visible'=>Yii::app()->user->role!='cashier'
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
