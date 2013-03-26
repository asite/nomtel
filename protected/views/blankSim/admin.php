<?php

$this->breadcrumbs = array(
	BlankSim::label(2) => array('admin'),
	Yii::t('app', 'List'),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('blank-sim-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo GxHtml::encode(BlankSim::label(2)); ?></h1>

<a class="btn" style="margin-bottom:10px;" href="<?php echo $this->createUrl('blankSim/add') ?>"><?php echo Yii::t('app', 'Create') ?></a>
<a class="btn" style="margin-bottom:10px;" href="<?php echo $this->createUrl('blankSim/balance') ?>">Складские остатки</a>
<a class="btn" style="margin-bottom:10px;" href="<?php echo $this->createUrl('blankSim/restoreStats') ?>">Статистика по восстановлениям</a>
<a class="btn" style="margin-bottom:10px;" href="<?php echo $this->createUrl('blankSim/prefixRegionList') ?>">Редактирование префиксов регионов</a>

<?php $this->widget('TbExtendedGridViewExport', array(
	'id' => 'blank-sim-grid',
	'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter' => $model,
	'columns' => array(
		array(
            'name'=>'type',
            'header'=>'Тип',
            'value'=>'BlankSim::getTypeLabel($data["type"])',
            'filter'=>BlankSim::getTypeDropDownList()
        ),
        array(
            'name'=>'icc',
            'header'=>'Icc'
		),
		array(
				'name'=>'operator_id',
                'header'=>'Оператор',
				'value'=>'$data["operator"]',
				'filter'=>Operator::getComboList(array(''=>'')),
				),
		array(
				'name'=>'operator_region_id',
                'header'=>'Регион',
				'value'=>'$data["operator_region"]',
				'filter'=>OperatorRegion::getGroupedDropDownList(array(''=>'')),
				),
		array(
                'name'=>'used_dt',
                'header'=>'Дата восстановления',
                'value'=>'new EDateTime($data["used_dt"])',
                'filter'=>false
        ),
                array(
                        'name'=>'used_support_operator_id',
                        'header'=>'Восстановивший оператор',
                        'value'=>'$data["surname"]." ".$data["name"]',
                        'filter'=>SupportOperator::getComboList(array(Yii::t('app','NOT RESTORED')=>Yii::t('app','NOT RESTORED'))),
                        ),
                array(
                        'name'=>'number',
                        'header'=>'Восстановленный номер'
                        ),
		array(
			'class' => 'bootstrap.widgets.TbButtonColumn',
            'htmlOptions' => array('style'=>'width:80px;text-align:center;vertical-align:middle'),
			'template'=>'{update} {delete}',
	        'deleteConfirmation' => Yii::t('app','Вы действительно хотите удалить эту пустышку?'),
            'updateButtonUrl'=>'Yii::app()->createUrl("blankSim/update",array("id"=>$data["id"]))',
            'deleteButtonUrl'=>'Yii::app()->createUrl("blankSim/delete",array("id"=>$data["id"]))',
		),
	),
)); ?>