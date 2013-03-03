<div style="margin-bottom:10px;border:1px black solid;">
    <div style="float:left;margin-right:20px;"><?=$data["dt"]?></div>
    <div style="float:left;margin-right:20px;"><?=$data["number"]?></div>
    <div style="float:left;margin-right:20px;"><?=$data["operator_title"]?></div>
    <div style="float:left;margin-right:20px;"><?=Number::getBalanceStatusLabel($data["balance_status"])?></div>
    <div style="float:left;margin-right:20px;"><?=$data["tariff_title"]?></div>
    <div style="float:left;margin-right:20px;"><?=Ticket::getStatusLabel($data["status"])?></div>
    <div style="clear:both;margin-top:5px;"><?=CHtml::encode($data["internal"])?></div>
<?php $ticket=$this->loadModel($data['id'],'Ticket');
$form=$this->beginWidget('BaseTbActiveForm',array('htmlOptions'=>array('class'=>'hide-labels'),'action'=>$this->createUrl('detail',array('id'=>$data['id'])))); ?>

    <?=$form->textareaRow($ticket,'response',array('style'=>'width:730px;margin-left:10px;','rows'=>5));?>
    <div style="text-align:right;margin-right:10px;">
        <?php $this->widget('bootstrap.widgets.TbButton',array(
        'buttonType'=>'submit',
        'htmlOptions'=>array('name'=>'refuse'),
        'label'=>'Отказать'
    ));?> &nbsp;
        <?php $this->widget('bootstrap.widgets.TbButton',array(
        'buttonType'=>'submit',
        'htmlOptions'=>array('name'=>'accept'),
        'label'=>'Выполнено'
    ));?>
    </div>

<?php $this->endWidget() ?>

</div>