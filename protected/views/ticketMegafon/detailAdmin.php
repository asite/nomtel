<h1><?=CHtml::encode($ticket)?></h1>

<div style="float:left;width:50%;">
    <?php $this->widget('bootstrap.widgets.TbDetailView',array(
    'data'=>$ticket->number,
    'attributes'=>array(
        array(
            'name'=>'number',
            'type'=>'html',
            'value'=>CHtml::link($ticket->number->number,Yii::app()->controller->createUrl("support/numberStatus",array("number"=>$ticket->number->number)))
        ),
        array(
            'name'=>'balance_status',
            'value'=>Number::getBalanceStatusLabel($ticket->number->balance_status)
        )
    ),
));?>
</div>

<div style="float:left;width:50%;">
    <?php $this->widget('bootstrap.widgets.TbDetailView',array(
    'data'=>$ticket->sim,
    'attributes'=>array(
        array(
            'name'=>'operator.title',
            'label'=>Operator::label()
        ),
        array(
            'name'=>'tariff.title',
            'label'=>Tariff::label()
        )
    ),
)); ?>
</div>


<?php $form=$this->beginWidget('BaseTbActiveForm',array('htmlOptions'=>array('class'=>'hide-labels'))); ?>
<fieldset><legend>Задание</legend>
<?=$form->textareaRow($ticket,'internal',array('label'=>'Текст обращения','disabled'=>true,'class'=>'span8','rows'=>5));?>
</fieldset>

<fieldset><legend>Ответ</legend>
<?=$form->textareaRow($ticket,'response',array('class'=>'span8','rows'=>5,'disabled'=>true));?>
</fieldset>

<?php $this->endWidget() ?>

<?php /*
echo '';
echo CHtml::htmlButton('<i class="icon-ok icon-white"></i> '.Yii::t('app', 'Save'), array('class'=>'btn btn-primary', 'type'=>'submit'));
echo '&nbsp;&nbsp;&nbsp;'.CHtml::htmlButton('<i class="icon-remove"></i> '.Yii::t('app', 'Cancel'), array('class'=>'btn', 'type'=>'button', 'onclick'=>'window.location.href="'.$this->createUrl('admin').'"'));
echo '</div>';
$this->endWidget();
*/ ?>

<h2>История</h2>
<?php $this->widget('TbExtendedGridViewExport', array(
    'id' => 'historygrid',
    'dataProvider' => $ticketHistory->search(),
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'columns' => array(
        array(
            'name'=>'support_operator_id',
            'header'=>'Кто',
            'value'=>'$data->supportOperator.$data->agent'
        ),
        array(
            'name'=>'dt',
        ),
        array(
            'name'=>'comment',
        ),
        array(
            'name'=>'status',
            'value'=>'TicketHistory::getStatusLabel($data->status)',
        ),
    ),
)); ?>