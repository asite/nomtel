<?php

$this->breadcrumbs = array(
    $bonusReport::model()->label(2) => array('list'),
    $bonusReport->adminLabel($bonusReport->label(1)),
);

?>

<h1><?php echo $bonusReport->adminLabel($bonusReport->label(1)); ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
    'data'=>$bonusReportAgent,
    'attributes'=>array(
        'sim_count',
        'sum',
        'sum_referrals',
    ),
)); ?>

<h2>По агентам</h2>

<?php $this->widget('TbExtendedGridViewExport', array(
    'id' => 'agent-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    //'filter' => $model,
    'columns' => array(
        array(
            'name'=>'agent_id',
            'value'=>'$data->agent',
            'filter'=>Agent::getComboList()
        ),
        array(
            'name'=>'sim_count',
            'htmlOptions' => array('style'=>'text-align:center;'),
        ),
        array(
            'name'=>'sum',
            'htmlOptions' => array('style'=>'text-align:center;'),
        ),
        array(
            'header'=>'',
            'htmlOptions' => array('style'=>'text-align:center;'),
            'type'=>'html',
            'value'=>'CHtml::link("Отчет",Yii::app()->controller->createUrl("bonusReport/download",array("id"=>$data->bonus_report_id,"agent_id"=>$data->agent_id)))'
        )
    ),
)); ?>

<h2>По номерам</h2>

<?php $this->widget('TbExtendedGridViewExport', array(
    'id' => 'number-grid',
    'dataProvider' => $dataProvider2,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter' => $bonusReportNumberSearch,
    'columns' => array(
        array(
            'name'=>'number',
            'header'=>Yii::t('app','Number'),
        ),
        array(
            'name'=>'tariff_id',
            'header'=>Yii::t('app','Tariff'),
            'filter'=>Tariff::getComboList(),
            'value'=>'$data["tariff"]'
        ),
        array(
            'name'=>'turnover',
            'header'=>Yii::t('app','Turnover'),
        ),
        array(
            'name'=>'rate',
            'value'=>'$data["rate"]!="" ? $data["rate"]."%":""',
            'header'=>Yii::t('app','Rate'),
        ),
        array(
            'name'=>'sum',
            'header'=>Yii::t('app','Sum'),
        ),
        array(
            'name'=>'status',
            'header'=>Yii::t('app','Status'),
            'filter'=>BonusReportNumber::getStatusDropDownList(),
            'value'=>'BonusReportNumber::getStatusLabel($data["status"])'
        ),
        array(
            'name'=>'agent_id',
            'header'=>Agent::label(1),
            'filter'=>Agent::getComboList(),
            'value'=>'$data["surname"]." ".$data["name"]'
        ),
    ),
)); ?>
