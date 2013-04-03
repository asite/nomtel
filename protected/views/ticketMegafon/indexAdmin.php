<style>
    .form-horizontal .controls {
        margin-left:0px;
        margin-bottom:10px;
    }
</style>
<h1>Список обращений</h1>

<?php $form=$this->beginWidget('BaseTbActiveForm',array(
    'id'=>'filter',
    'type'=>'horizontal'
)); ?>

<div class="form-container-horizontal">
    <div class="form-container-item form-label-width-100">
        <?=$form->textFieldRow($model,'number',array('class'=>'span2'))?>
        <?php echo $form->PickerDateRow($model,'dt',array('class'=>'span2','errorOptions'=>array('hideErrorMessage'=>true)),array('minYearDelta'=>5,'maxYearDelta'=>0,'onSelect'=>'js:function(){$(this).closest("form").submit();}'))?>
    </div>
    <div class="form-container-item form-label-width-140">
        <?=$form->dropDownListRow($model,'status',Ticket::getStatusDropDownList(array(''=>'')),array('class'=>'span2'))?>
        <?=$form->dropDownListRow($model,'megafon_status',Ticket::getMegafonStatusDropDownList(array(''=>'')),array('class'=>'span2'))?>
    </div>
</div>


<?php $this->endWidget(); ?>



<div style="clear:both;">
<?php $this->widget('bootstrap.widgets.TbListView',array(
    'dataProvider'=>$dataProvider,
    'itemView'=>'_itemAdmin',
    'id'=>'listview',
));?>
</div>

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
