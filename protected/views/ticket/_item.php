<div class="li-item">
    <table class="table table-striped table-condensed">
        <tr>
            <th>Дата</th>
            <td><?=new EDateTime($data["dt"],null,'date')?></td>
            <th>Статус баланса</th>
            <td><?=Number::getBalanceStatusLabel($data["balance_status"])?></td>
            <th>Оператор</th>
            <td><?=$data["operator_title"]?></td>
        </tr>
        <tr>
            <th>Номер</th>
            <td><?=CHtml::link($data["number"],$this->createUrl('support/numberStatus',array('number'=>$data['number'])))?></td>
            <th>Статус обращения</th>
            <td><?=Ticket::getStatusLabel($data["status"])?></td>
            <th>Тариф</th>
            <td><?=$data["tariff_title"]?></td>
        </tr>
    </table>
    <div class="comment"><?=CHtml::encode($data["internal"])?></div>
    <div class="button-row">
        <?php $this->widget('bootstrap.widgets.TbButton',array(
            'url'=>$this->createUrl($this->action->id=='index' ? 'assignToMe':'detail',array('id'=>$data["id"])),
            'label'=>'Приступить'
        ));?>
    </div>
</div>