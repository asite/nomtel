<h1><?php echo GxHtml::encode($model->label(1)); ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
    'data'=>$model,
    'attributes'=>array(
        'id',
        array(
            'label'=>Yii::t('app','shipped'),
            'value'=>$model->agent->parent
        ),
        array(
            'label'=>Yii::t('app','took'),
            'value'=>$model->agent
        ),
        'sum',
        array(
            'label'=>Yii::t('app','Payment of the №'),
            'value'=>$model->id
        ),
        'dt',
        array(
            'label'=>Yii::t('app','Signature'),
            'value'=>'',
        ),
    ),
)); ?>
<br/><br/><br/>
<h2><?php echo GxHtml::encode(Sim::model()->label(2)); ?></h2>
<?php
    $dataProvider = $sim->search();
    $dataProvider->pagination->pageSize = $dataProvider->totalItemCount;

    $this->widget('bootstrap.widgets.TbExtendedGridView', array(
    'id' => 'delivery-report-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'enableSorting'=>false,
    'columns' => array(
        array(
            'name'=>'number',
            'htmlOptions' => array('style'=>'text-align:center;'),
        ),
        array(
            'name'=>'icc',
            'value'=>'$data->shortIcc',
            'htmlOptions' => array('style'=>'text-align:center;'),
        ),
        array(
            'name'=>'operator_id',
            'value'=>'$data->operator',
            'htmlOptions' => array('style'=>'text-align:center;'),
            'filter'=>Operator::getComboList(),
        ),
        array(
            'name'=>'tariff_id',
            'value'=>'$data->tariff',
            'htmlOptions' => array('style'=>'text-align:center;'),
            'filter'=>Tariff::getComboList(),
        ),
        array(
            'name'=>'number_price',
            'htmlOptions' => array('style'=>'text-align:center;'),
            'filter'=>false,
            ),
        array(
            'name'=>'sim_price',
            'htmlOptions' => array('style'=>'text-align:center;'),
            'filter'=>false,
        )
    ),
)); ?>