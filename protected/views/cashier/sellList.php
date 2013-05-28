<h1>Продажа</h1>

<?php $this->widget('TbExtendedGridViewExport', array(
    'id' => 'number-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter' => $model,
    'columns' => array(
        array(
            'name'=>'number',
            'header'=>Yii::t('app','Number'),
        ),
        array(
            'name'=>'tariff_id',
            'value'=>'$data["tariff"]',
            'filter'=>Tariff::getComboList(),
            'header'=>Tariff::label(1),
        ),
        array(
            'name'=>'operator_region',
            'value'=>'$data["operator_region"]',
            'filter'=>false,
            'header'=>OperatorRegion::label(1),
        ),
        array(
            'name'=>'balance',
            'value'=>'$data["balance"]',
            'filter'=>false,
            'header'=>Yii::t('app','Balance'),
        ),
        array(
            'name'=>'balance_changed_dt',
            'value'=>'$data["balance_changed_dt"]!="" ? new EDateTime($data["balance_changed_dt"],null,"date"):""',
            'filter'=>false,
            'header'=>'Баланс изменился',
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'htmlOptions' => array('style'=>'width:40px;text-align:center;vertical-align:middle'),
            'template'=>'{sell}',
            'buttons'=>array(
                'sell'=>array(
                    'label'=>'Продажа',
                    //'icon'=>'envelope',
                    'url'=>'Yii::app()->controller->createUrl("cashier/sell",array("id"=>$data["id"]))',
                ),
            )
        ),
    ),
)); ?>
