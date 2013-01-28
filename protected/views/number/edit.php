<?php if (Yii::app()->user->role=='admin'): ?>

<?php

$this->breadcrumbs = array(
    Sim::label(2)=>$this->createUrl('sim/list'),
    $number->adminLabel(Number::label(1))
);

?>

<h1><?=$number->adminLabel(Number::label(1))?></h1>

<div class="number_info">
    <div class="w80 cfix">
        <div style="float:left;width:40%;">
        <?php $this->widget('bootstrap.widgets.TbDetailView',array(
            'htmlOptions' => array('class'=> 'table margin_b0 table-striped table-condensed'),
            'data'=>$number,
            'attributes'=>array(
                'number'
            ),
        )); ?>
        <?php $this->widget('bootstrap.widgets.TbEditableDetailView',array(
            'htmlOptions' => array('class'=> 'table margin_b0 table-condensed'),
            'data'=>$tariff,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','Tariff'),
                    'name' => 'title',
                    'value' => Chtml::value($tariff, 'title'),
                    'editable' => array(
                        'type'   => 'select',
                        'url' => $this->createUrl('number/saveTariff',array('id'=>$number->id)),
                        'options' => array(
                            'params' => array('YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
                        ),
                        'source' => CHtml::listData(Tariff::model()->findAllByAttributes(array('operator_id'=>$number->sim->operator_id)), 'id', 'title'),
                        'success'   => 'js: function(data) {
                            $.fn.yiiGridView.update("number-grid");
                        }'

                    )
                )
            ),
        )); ?>
        <?php $this->widget('bootstrap.widgets.TbDetailView',array(
            'htmlOptions' => array('class'=> 'table table-striped table-condensed'),
            'data'=>$number,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','the Agent'),
                    'value'=>$sim->parentAgent
                    )
            ),
        )); ?>
        </div>
        <div style="float:left;width:40%;">
        <?php $this->widget('bootstrap.widgets.TbDetailView',array(
            'htmlOptions' => array('class'=> 'table margin_b0 table-striped table-condensed'),
            'data'=>$number,
            'attributes'=>array(
                'sim.operator'
            ),
        )); ?>
        <?php $this->widget('bootstrap.widgets.TbEditableDetailView',array(
            'htmlOptions' => array('class'=> 'table margin_b0 table-condensed'),
            'data'=>$operatorRegion,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','OperatorRegion'),
                    'name' => 'title',
                    'value' => Chtml::value($operatorRegion, 'title'),
                    'editable' => array(
                        'type'   => 'select',
                        'url' => $this->createUrl('number/saveOperatorRegion',array('id'=>$number->id)),
                        'options' => array(
                            'params' => array('YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
                        ),
                        'source' => CHtml::listData(OperatorRegion::model()->findAllByAttributes(array('operator_id'=>$number->sim->operator_id)), 'id', 'title'),
                        'success'   => 'js: function(data) {
                            $.fn.yiiGridView.update("number-grid");
                        }'
                    )
                )
            ),
        )); ?>
        <?php $this->widget('bootstrap.widgets.TbDetailView',array(
            'htmlOptions' => array('class'=> 'table table-striped table-condensed'),
            'data'=>$number,
            'attributes'=>array(
                'sim.icc'
            ),
        )); ?>
        </div>
        <div style="float:left;width:20%;">
        <?php $this->widget('bootstrap.widgets.TbDetailView',array(
            'htmlOptions' => array('class'=> 'table table-striped table-condensed'),
            'data'=>$number,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','Status'),
                    'value'=>Yii::t('app',$number->status),
                    ),
                array(
                    'label'=>'&nbsp;',
                    'value'=>''
                    ),
                array(
                    'label'=>'&nbsp;',
                    'value'=>''
                    )
            ),
        )); ?>
        </div>
    </div>

    <br/>
    <div class="cfix">
        <div style="float:left;width:40%;">
        <?php $this->widget('bootstrap.widgets.TbEditableDetailView',array(
            'htmlOptions' => array('class'=> 'table margin_b0 table-striped table-condensed'),
            'data'=>$company,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','Company'),
                    'name' => 'title',
                    'value' => Chtml::value($company, 'title'),
                    'editable' => array(
                        'type'   => 'select',
                        'url' => $this->createUrl('number/saveCompany',array('id'=>$number->id)),
                        'options' => array(
                            'params' => array('YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
                        ),
                        'source' => CHtml::listData(Company::model()->findAll(),'id', 'title'),
                        'success'   => 'js: function(data) {
                            $.fn.yiiGridView.update("number-grid");
                        }'
                    )
                )
            ),
        )); ?>
        <?php $this->widget('bootstrap.widgets.TbEditableDetailView',array(
            'htmlOptions' => array('class'=>'table margin_b0 table-condensed'),
            'data'=>$number,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','Date connection'),
                    'type'=>'html',
                    'value'=>$SubscriptionAgreement->dt
                    )
            ),
        )); ?>
        </div>
        <div style="float:left;width:30%;">
        <?php $this->widget('bootstrap.widgets.TbEditableDetailView',array(
            'htmlOptions' => array('class'=> 'table table-striped table-condensed'),
            'data'=>$number,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','Director'),
                    'value'=>''
                    ),
                array(
                    //'label'=>Yii::t('app','Company'),
                    'name' => 'codeword',
                    'value' => $number->codeword,
                    'editable' => array(
                        'type'   => 'text',
                        'url' => $this->createUrl('number/saveCodeword',array('id'=>$number->id)),
                        'options' => array(
                            'params' => array('YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
                        ),
                        'source' => $number->codeword,
                        'success'   => 'js: function(data) {
                            $.fn.yiiGridView.update("number-grid");
                        }'
                    )
                )
            ),
        )); ?>
        </div>
        <div style="float:left;width:30%;">
        <?php $this->widget('bootstrap.widgets.TbEditableDetailView',array(
            'htmlOptions' => array('class'=> 'table table-striped table-condensed'),
            'data'=>$number,
            'attributes'=>array(
                array(
                    //'label'=>Yii::t('app','Company'),
                    'name' => 'service_password',
                    'value' => $number->service_password,
                    'editable' => array(
                        'type'   => 'text',
                        'url' => $this->createUrl('number/saveServicePassword',array('id'=>$number->id)),
                        'options' => array(
                            'params' => array('YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
                        ),
                        'source' => $number->service_password,
                        'success'   => 'js: function(data) {
                            $.fn.yiiGridView.update("number-grid");
                        }'
                    )
                ),
                array(
                    'label'=>'&nbsp;',
                    'value'=>''
                ),
            ),
        )); ?>
        </div>
    </div>

    <br/>
    <div class="table_margin0 cfix">
        <div style="float:left;width:40%;">
        <?php   $this->widget('bootstrap.widgets.TbDetailView',array(
            'htmlOptions' => array('class'=> 'table table-striped table-condensed'),
            'data'=>$number,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','BAD'),
                    'value'=>''
                ),
                'personal_account',
            ),
        )); ?>
        </div>
        <div style="float:left;width:30%;">
        <?php $this->widget('bootstrap.widgets.TbDetailView',array(
            'htmlOptions' => array('class'=> 'table table-striped table-condensed'),
            'data'=>$number,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','Sim Price'),
                    'value'=>$sim->sim_price
                    ),
                array(
                    'label'=>'&nbsp;',
                    'value'=>''
                    )
            ),
        )); ?>
        </div>
        <div style="float:left;width:30%;">
        <?php $this->widget('bootstrap.widgets.TbDetailView',array(
            'htmlOptions' => array('class'=> 'table table-striped table-condensed'),
            'data'=>$number,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','Number Price'),
                    'value'=>$sim->number_price
                    ),
                array(
                    'label'=>'&nbsp;',
                    'value'=>''
                    )
            ),
        )); ?>
        </div>
    </div>
    <div class="cfix">
        <?php

        if ($BalanceReportNumber->balanceReport->dt) $turnover = '('.$BalanceReportNumber->balanceReport->dt.'): ';
        if ($BalanceReportNumber->balance) $turnover = $BalanceReportNumber->balance.$turnover;

        $this->widget('bootstrap.widgets.TbDetailView',array(
            'htmlOptions' => array('class'=> 'table table-striped table-condensed'),
            'data'=>$number,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','Turnover sim'),
                    'value'=>$turnover
                    )
            ),
        )); ?>
    </div>
</div>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'number-grid',
    'dataProvider' => $numberHistory->search(),
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'columns' => array(
        'dt',
        'who',
        array(
            'name'=>'comment',
            'type'=>'html',
            'value'=>'NumberHistory::replaceShortcutsByLinks($data->comment)',
        ),
    ),
)); ?>
<?php endif; ?>