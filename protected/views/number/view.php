<?php

$this->breadcrumbs = array(
    Sim::label(2)=>$this->createUrl('sim/list'),
    $number->adminLabel(Number::label(1))
);

?>

<h1><?=$number->adminLabel(Number::label(1))?></h1>

<?php
if ($number->status!=Number::STATUS_FREE) {
    $this->widget('bootstrap.widgets.TbButton',array(
        'url'=>$this->createUrl('subscriptionAgreement/update',array('number_id'=>$number->id)),
        'label'=>SubscriptionAgreement::label()
    ));
}
?>  <br/><br/>

<div class="number_info">
    <div class="w80 cfix">
        <div style="float:left;width:40%;">
        <?php $this->widget('bootstrap.widgets.TbDetailView',array(
            'data'=>$number,
            'attributes'=>array(
                'number',
                array(
                    'label'=>Yii::t('app','Tariff')."<br/><br/>",
                    'value'=>$sim->tariff
                    ),
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
                array(
                    'label'=>Yii::t('app','OperatorRegion')."<br/><br/>",
                    'value'=>$sim->operatorRegion
                    ),
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
                    'label'=>Yii::t('app','Balance Status'),
                    'value'=>''
                    ),
                array(
                    'label'=>Yii::t('app','Abonent'),
                    'value'=>$SubscriptionAgreement->person
                    )
            ),
        )); ?>
        </div>
    </div>
    <?php if (Yii::app()->user->role=='admin' || Yii::app()->user->role=='supportSuper' || Yii::app()->user->role=='support'): ?>
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
    <?php if (Yii::app()->user->role=='admin' || Yii::app()->user->role=='supportSuper'): ?>
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
                    'label'=>Yii::t('app','Balance'),
                    'value'=>Number::getBalanceStatusLabel($number->balance_status)
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

        if ($BonusReportNumber->bonusReport->dt) $turnover = '('.$BonusReportNumber->bonusReport->dt.'): ';
        if ($BonusReportNumber->turnover) $turnover = $BonusReportNumber->turnover.$turnover;

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

<?php if (Yii::app()->user->role=='admin' || Yii::app()->user->role=='support' || Yii::app()->user->role=='supportSuper'): ?>
<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'number-grid',
    'dataProvider' => $numberHistory->search(),
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'columns' => array(
        'dt',
        array(
            'name'=>'who',
            'type'=>'html',
            'value'=>'NumberHistory::replaceShortcutsByLinks($data->who)',
        ),
        array(
            'name'=>'comment',
            'type'=>'html',
            'value'=>'NumberHistory::replaceShortcutsByLinks($data->comment)',
        ),
    ),
)); ?>
<?php endif; ?>