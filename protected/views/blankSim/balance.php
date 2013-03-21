<h1>Складские остатки пустышек</h1>

<?php $this->widget('TbExtendedGridViewExport', array(
    'id' => 'blank-sim-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'columns' => array(
        array(
            'name'=>'operator',
            'header'=>Operator::label(),
        ),
        array(
            'name'=>'region',
            'header'=>OperatorRegion::label(),
        ),
        array(
            'name'=>BlankSim::TYPE_NORMAL,
            'header'=>BlankSim::getTypeLabel(BlankSim::TYPE_NORMAL),
            'htmlOptions'=>array('style'=>'text-align:center;')
        ),
        array(
            'name'=>BlankSim::TYPE_MICRO,
            'header'=>BlankSim::getTypeLabel(BlankSim::TYPE_MICRO),
            'htmlOptions'=>array('style'=>'text-align:center;')
        ),
        array(
            'name'=>BlankSim::TYPE_NANO,
            'header'=>BlankSim::getTypeLabel(BlankSim::TYPE_NANO),
            'htmlOptions'=>array('style'=>'text-align:center;')
        ),
    ),
)); ?>