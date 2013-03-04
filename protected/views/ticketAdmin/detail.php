<h1><?=CHtml::encode($ticket)?></h1>

<div style="float:left;width:50%;">
    <?php $this->widget('bootstrap.widgets.TbDetailView',array(
    'data'=>$ticket->number,
    'attributes'=>array(
        'number',
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
<fieldset><legend>Текст обращения</legend>
<?=$form->textareaRow($ticket,'text',array('label'=>'Текст обращения','disabled'=>true,'class'=>'span8','rows'=>5));?>
</fieldset>

<div style="margin-bottom:10px;">
<?php $this->widget('bootstrap.widgets.TbButton',array(
    'htmlOptions'=>array('onclick'=>'$("#Ticket_internal").val($("#Ticket_text").val());'),
    'label'=>'Скопировать'
));?>
</div>

<?=$form->textareaRow($ticket,'internal',array('class'=>'span8','rows'=>5));?>

<div style="text-align:right;">
    <?php $this->widget('bootstrap.widgets.TbButton',array(
    'buttonType'=>'submit',
    'htmlOptions'=>array('name'=>'reject'),
    'label'=>'Отказать'
));?> &nbsp;
<?php $this->widget('bootstrap.widgets.TbButton',array(
    'buttonType'=>'submit',
    'htmlOptions'=>array('name'=>'toOperators'),
    'label'=>'Передать операторам'
));?> &nbsp;
<?php $this->widget('bootstrap.widgets.TbButton',array(
    'buttonType'=>'submit',
    'htmlOptions'=>array('name'=>'toMegafon'),
    'label'=>'Передать в мегафон'
));?> &nbsp;
<?php $this->widget('bootstrap.widgets.TbButton',array(
    'buttonType'=>'submit',
    'htmlOptions'=>array('name'=>'done'),
    'label'=>'Выполнено'
));?>
</div>

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