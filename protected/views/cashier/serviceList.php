<h1>Обслуживание</h1>

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
            'value'=>'$data["agent_surname"]." ".$data["agent_name"]',
            'filter'=>false,
            'header'=>Agent::label(),
        ),
        array(
            'name'=>'last_balance',
            'value'=>'$data["last_balance"]',
            'filter'=>false,
            'header'=>Yii::t('app','Balance'),
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'htmlOptions' => array('style'=>'width:40px;text-align:center;vertical-align:middle'),
            'template'=>'{sell}',
            'buttons'=>array(
                'sell'=>array(
                    'label'=>'Карточка номера',
                    //'icon'=>'envelope',
                    'url'=>'Yii::app()->controller->createUrl("number/view",array("id"=>$data["id"]))',
                ),
            )
        ),
    ),
)); ?>
