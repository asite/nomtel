<h1>Список обращений</h1>

<?php $form=$this->beginWidget('BaseTbActiveForm',array(
    'id'=>'filter',
    'type'=>'horizontal'
)); ?>

<div class="form-container-horizontal">
    <div class="form-container-item form-label-width-60">
        <?=$form->textFieldRow($model,'number',array('class'=>'span2'))?>
    </div>
</div>

<div class="form-container-horizontal">
    <div class="form-container-item form-label-width-60">
        <?php echo $form->PickerDateRow($model,'dt',array('class'=>'span2','errorOptions'=>array('hideErrorMessage'=>true)),array('minYearDelta'=>5,'maxYearDelta'=>0,'onSelect'=>'js:function(){$(this).closest("form").submit();}'))?>
    </div>
</div>

<div class="form-container-horizontal">
    <div class="form-container-item form-label-width-60">
        <?=$form->dropDownListRow($model,'status',Ticket::getStatusDropDownList(array(''=>'')),array('class'=>'span2'))?>
    </div>
</div>
<?php $this->endWidget(); ?>



<div style="clear:both;">
<?php $this->widget('bootstrap.widgets.TbListView',array(
    'dataProvider'=>$dataProvider,
    'itemView'=>'_item',
    'id'=>'listview',
));?>

<script>
    $('#filter').submit(function(){
        var data=$(this).serialize().replace(/YII_CSRF_TOKEN[^&]*/,'');
        $.fn.yiiListView.update('listview', {
            data:data
        });
        return false;
    });

    $('#filter').on('keydown',function(event){
        if (event.type === 'keydown' && event.keyCode == 13) $('#filter').submit();
    });

    $('#filter select').on('change',function(event){
        $('#filter').submit();
    });
</script>
</div>
