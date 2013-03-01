<h2><?php echo Yii::t('app', 'Support List'); ?></h2>

<?php $this->widget('TbExtendedGridViewExport', array(
    'id' => 'ticket-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'columns' => array(
        'id',
        'dt',
        'text',
        'response',
        array(
            'name'=>'status',
            'value'=>'Ticket::getStatusPOLabel($data->status)',
            'htmlOptions'=>array('style'=>'width:120px;'),
        )
    ),
)); ?>