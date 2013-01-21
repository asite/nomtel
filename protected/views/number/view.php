<?php

$this->breadcrumbs = array(
    Number::label(2)=>$this->createUrl('list'),
    $number->adminLabel(Number::label(1))
);

?>

<h1><?=$number->adminLabel(Number::label(1))?></h1>

<div class="cfix">
    <div style="float:left;width:45%;">
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
    <div style="float:left;width:30%;">
    <?php $this->widget('bootstrap.widgets.TbDetailView',array(
        'data'=>$number,
        'attributes'=>array(
            'sim.operator',
            'sim.operatorRegion',
            'sim.icc'
        ),
    )); ?>
    </div>
    <div style="float:left;width:25%;">
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
<br/>
<div class="cfix">
    <div style="float:left;width:45%;">
    <?php $this->widget('bootstrap.widgets.TbDetailView',array(
        'data'=>$number,
        'attributes'=>array(
            array(
                'label'=>Yii::t('app','BAD'),
                'value'=>''
            ),
            'personal_account',
            array(
                'label'=>Yii::t('app','Turnover sim'),
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
                'label'=>Yii::t('app','Sim Price'),
                'value'=>$sim->sim_price
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
    <div style="float:left;width:25%;">
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
                ),
            array(
                'label'=>'&nbsp;',
                'value'=>''
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
        'comment'
    ),
)); ?>