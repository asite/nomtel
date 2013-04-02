<?php

$this->breadcrumbs = array(
    Number::label(2),
);

?>

<h1>Редактирование префиксов регионов</h1>

<a class="btn" style="margin-bottom:10px;" href="<?php echo $this->createUrl('blankSim/addPrefix') ?>">Добавить префикс</a>

<?php $this->widget('TbExtendedGridViewExport', array(
    'id' => 'number-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter' => $model,
    'columns' => array(
        'icc_prefix',
        array(
            'name'=>'operator_id',
            'header'=>'Оператор',
            'value'=>'$data["operator"]',
            'filter'=>Operator::getComboList(),
        ),
        array(
            'name'=>'operator_region_id',
            'header'=>'Регион',
            'value'=>'$data["operatorRegion"]',
            'filter'=>OperatorRegion::getGroupedDropDownList(array(''=>'')),
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'htmlOptions' => array('style'=>'width:80px;text-align:center;vertical-align:middle'),
            'template'=>'{update} {delete}',
            'deleteConfirmation' => Yii::t('app','Вы действительно хотите удалить эту пустышку?'),
            'updateButtonUrl'=>'Yii::app()->createUrl("blankSim/updatePrefix",array("id"=>$data["id"]))',
            'deleteButtonUrl'=>'Yii::app()->createUrl("blankSim/deletePrefix",array("id"=>$data["id"]))',
        ),
    ),
)); ?>