<?php

$this->breadcrumbs = array(
    Sim::label(2)=>$this->createUrl('sim/list'),
    $number->adminLabel(Number::label(1))
);

?>

<h1><?=$number->adminLabel(Number::label(1))?></h1>

<?php
$this->widget('bootstrap.widgets.TbButton',array(
    'url'=>Yii::app()->user->checkAccess("updateSubscriptionAgreement",array("parent_agent_id"=>$sim->parent_agent_id,"number_status"=>$number->status)) ?
        $this->createUrl('subscriptionAgreement/update',array('number_id'=>$number->id))
        :
        $this->createUrl("subscriptionAgreement/startCreate",array("sim_id"=>$number->sim_id)),
    'label'=>SubscriptionAgreement::label()
));
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
                array(
                    'label'=>'<a href="'.$this->createUrl('agentChangeIcc',array('sim_id'=>$sim->id)).'" class="btn btn-primary" style="padding: 0 5px;">icc</a> '.Yii::t('app','ICC'),
                    'value'=>$sim->icc
                    ),
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

<?php if (Yii::app()->user->role=='admin' || Yii::app()->user->role=='support' || Yii::app()->user->role=='supportSuper' || Yii::app()->user->role=='cashier'): ?>
<h2>История номера</h2>
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
<h2>История баланса</h2>
<?php $this->widget('bootstrap.widgets.TbGridView', array(
        'id' => 'balances-grid',
        'dataProvider' => $balancesDataProvider,
        'itemsCssClass' => 'table table-striped table-bordered table-condensed',
        'columns' => array(
            array(
                'header'=>'Дата',
                'value'=>'new EDateTime($data["dt"],null,"date")'
            ),
            array(
                'name'=>'balance',
                'header'=>'Баланс',
            ),
        ),
    )); ?>
<?php if ($numberLastInfo) { ?>
    <h2>Актуальная информация</h2>
    <div>
        <?php
        $p = new CHtmlPurifier();
        echo $p->purify($numberLastInfo->text);
        ?>
    </div>
    <?php } ?>
<h2>Тикеты</h2>

<style>#addTicket label {display:none;}</style>
<div id="addTicket">
    <?php
    $form = $this->beginWidget('BaseTbActiveForm', array(
        'id' => 'add-ticket',
        'enableAjaxValidation' => true,
        'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false)
    ));
    ?>
    <?php echo $form->errorSummary($addTicket); ?>
    <?=$form->textareaRow($addTicket,'text',array('style'=>'width:100%;','rows'=>3,'errorOptions'=>array('hideErrorMessage'=>true)));?><br/>
    <?php echo CHtml::htmlButton('Написать в мегафон', array('style'=>'float:right','class'=>'btn', 'type'=>'submit')); ?>
    <div class="clear"></div>
    <?php $this->endWidget(); ?>
</div>


<?php $this->widget('ootstrap.widgets.TbGridView', array(
        'id' => 'ticket-grid',
        'dataProvider' => $ticketsDataProvider,
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
<?php endif; ?>