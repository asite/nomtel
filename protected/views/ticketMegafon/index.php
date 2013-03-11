<style>
    .form-horizontal .controls {
        margin-left:0px;
    }
</style>
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
        <?php echo $form->maskFieldRow($model,'dt','99.99.9999',array('class'=>'span2','errorOptions'=>array('hideErrorMessage'=>true))); ?>
    </div>
</div>

<?php $this->endWidget(); ?>



<div style="clear:both;">
<?php $this->widget('bootstrap.widgets.TbListView',array(
    'dataProvider'=>$dataProvider,
    'itemView'=>'_item',
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
