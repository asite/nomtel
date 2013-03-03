<div style="margin-bottom:10px;border:1px black solid;">
    <div style="float:left;margin-right:20px;"><?=$data["dt"]?></div>
    <div style="float:left;margin-right:20px;"><?=CHtml::link($data["number"],$this->createUrl('support/numberStatus',array('number'=>$data['number'])))?></div>
    <div style="float:left;margin-right:20px;"><?=$data["operator_title"]?></div>
    <div style="float:left;margin-right:20px;"><?=Number::getBalanceStatusLabel($data["balance_status"])?></div>
    <div style="float:left;margin-right:20px;"><?=$data["tariff_title"]?></div>
    <div style="float:left;margin-right:20px;"><?=Ticket::getStatusLabel($data["status"])?></div>
    <div style="clear:both;margin-top:5px;"><?=CHtml::encode($data["internal"])?></div>
    <div style="text-align:right;margin:5px;">
        <?php $this->widget('bootstrap.widgets.TbButton',array(
            'url'=>$this->createUrl($this->action->id=='index' ? 'assignToMe':'detail',array('id'=>$data["id"])),
            'label'=>'Приступить'
        ));?>
    </div>
</div>