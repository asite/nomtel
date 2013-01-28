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
            'data'=>$number,
            'attributes'=>array(
                'number',
                'sim.tariff',
                array(
                    'label'=>Yii::t('app','the Agent'),
                    'value'=>$sim->parentAgent
                    )
            ),
        )); ?>
        </div>
        <div style="float:left;width:40%;">
        <?php $this->widget('bootstrap.widgets.TbDetailView',array(
            'data'=>$number,
            'attributes'=>array(
                'sim.operator',
                'sim.operatorRegion',
                'sim.icc'
            ),
        )); ?>
        </div>
        <div style="float:left;width:20%;">
        <?php $this->widget('bootstrap.widgets.TbDetailView',array(
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
    <?php if (Yii::app()->user->role=='admin' || Yii::app()->user->role=='support'): ?>
    <br/>
    <div class="cfix">
        <div style="float:left;width:50%;">
        <?php $this->widget('bootstrap.widgets.TbDetailView',array(
            'data'=>$number,
            'attributes'=>array(
                'sim.company',
                array(
                    'label'=>Yii::t('app','Date connection'),
                    'type'=>'html',
                    'value'=>$SubscriptionAgreement->dt
                    )
            ),
        )); ?>
        </div>
        <div style="float:left;width:50%;">
        <?php $this->widget('bootstrap.widgets.TbDetailView',array(
            'data'=>$number,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','Director'),
                    'value'=>''
                    ),
                array(
                    'label'=>Yii::t('app','Codeword'),
                    'value'=>''
                    ),
            ),
        )); ?>
        </div>
    </div>
    <?php endif; ?>
    <?php if (Yii::app()->user->role=='admin'): ?>
    <br/>
    <div class="table_margin0 cfix">
        <div style="float:left;width:40%;">
        <?php   $this->widget('bootstrap.widgets.TbDetailView',array(
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
            'data'=>$number,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','Turnover sim'),
                    'value'=>$turnover
                    )
            ),
        )); ?>
    </div>
    <?php endif; ?>
</div>

<?php if (Yii::app()->user->role=='admin' || Yii::app()->user->role=='support'): ?>
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