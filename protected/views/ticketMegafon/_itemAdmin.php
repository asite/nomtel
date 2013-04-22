<div class="li-item">
    <table class="table table-striped table-condensed">
        <tr>
            <th>Дата</th>
            <td><?=new EDateTime($data["dt"])?></td>
            <th>Статус обращения</th>
            <td><?=Ticket::getStatusLabel($data["status"])?></td>
            <th>Оператор</th>
            <td><?=$data["operator_title"]?></td>
        </tr>
        <tr>
            <th>Номер</th>
            <td><?=CHtml::link($data["number"],$this->createUrl('support/numberStatus',array('number'=>$data['number'])))?></td>
            <th>Статус мегафона</th>
            <td><?=Ticket::getMegafonStatusLabel($data["megafon_status"])?></td>
            <th>Тариф</th>
            <td><?=$data["tariff_title"]?></td>
        </tr>
    </table>
    <div class="comment"><?=CHtml::encode($data["internal"])?></div>
<?php $ticket=$this->loadModel($data['id'],'Ticket');
$form=$this->beginWidget('BaseTbActiveForm',array('type'=>'horizontal','htmlOptions'=>array('class'=>'hide-labels'),'action'=>$this->createUrl('detail',array('id'=>$data['id'])),'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false))); ?>
   <hr style="margin:5px;0px;">
    <div class="comment"><?=CHtml::encode($data["response"])?></div>
    <div class="button-row">
        <?php $this->widget('bootstrap.widgets.TbButton',array(
        'url'=>$this->createUrl('detailAdmin',array('id'=>$data['id'])),
        'label'=>'Посмотреть'
    ));?>
    <?php $this->endWidget() ?>
   <?php $ticket=$this->loadModel($data['id'],'Ticket');
?></div><?
$form=$this->beginWidget('BaseTbActiveForm',array('type'=>'horizontal','htmlOptions'=>array('class'=>'hide-labels'),'action'=>$this->createUrl('detail',array('id'=>$data['id'])),'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false))); ?>

   
    <div class="button-row">
        <input type="hidden" name="Ticket[response]" value="">
        <?php $this->widget('bootstrap.widgets.TbButton',array(
        'buttonType'=>'submit',
        'htmlOptions'=>array('name'=>'download'),
        'label'=>'Скачать заявление'
    ));?>
     
    </div>

<?php $this->endWidget() ?>

</div>