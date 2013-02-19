<?php

$this->breadcrumbs = array(
    Number::label(2),
);

?>

<h1><?=Number::label(2)?></h1>

<?php $this->widget('TbExtendedGridViewExport', array(
    'id' => 'number-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter' => $model,
    'columns' => array(
        'personal_account',
        'number',
        array(
            'name'=>'status',
            'filter'=>Number::getStatusDropDownList(),
            'value'=>'Number::getStatusLabel($data->status)',
            'htmlOptions'=>array('style'=>'width:120px;'),
        ),
        array(
            'name'=>'balance_status',
            'filter'=>Number::getBalanceStatusDropDownList(),
            'value'=>'Number::getBalanceStatusLabel($data->balance_status)',
        ),
        array(
            'name'=>'balance_status_changed_dt',
            'filter'=>false
        ),
		array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'htmlOptions' => array('style'=>'width:80px;text-align:center;vertical-align:middle'),
            'template'=>'{view}',
            'buttons'=>array(
                'view'=>array(
                    'url'=>'Yii::app()->createUrl("number/".((Yii::app()->user->role=="admin")?"edit":"view"),array("id"=>$data->id))',
                ),
            ),
        ),
    ),
)); ?>