<h1>Список обращений</h1>

<?php
$this->widget('bootstrap.widgets.TbTabs', array(
    'type'=>'tabs',
    'tabs'=>array(
        array('label'=>'Все обращения', 'content'=>'', 'active'=>$this->action->id=='index','id'=>'tab1'),
        array('label'=>'Мои обращения', 'content'=>'', 'active'=>$this->action->id=='indexMy','id'=>'tab2'),
    ),
));
?>
<script>
    $('.nav-tabs a[href=#tab1]').on('click',function(){window.location.href='<?=$this->createUrl('index');?>';return false;});
    $('.nav-tabs a[href=#tab2]').on('click',function(){window.location.href='<?=$this->createUrl('indexMy')?>';return false;});
</script>

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