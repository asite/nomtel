<div class="li-item">
    <table class="table table-striped table-condensed">
        <tr>
            <th>Дата</th>
            <td><?=new EDateTime($data["dt"])?></td>
            <th>Номер</th>
            <td><?=$data["number"]?></td>
        </tr>
    </table>
    <div class="comment"><?=CHtml::encode($data["internal"])?></div>
<?php $ticket=$this->loadModel($data['id'],'Ticket');
$form=$this->beginWidget('BaseTbActiveForm',array('htmlOptions'=>array('class'=>'hide-labels'),'action'=>$this->createUrl('detail',array('id'=>$data['id'])))); ?>

    <?=$form->textareaRow($ticket,'response',array('rows'=>5));?>
    <div class="button-row">
        <?php $this->widget('bootstrap.widgets.TbButton',array(
        'buttonType'=>'submit',
        'htmlOptions'=>array('name'=>'download'),
        'label'=>'Скачать заявление'
    ));?> &nbsp;
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