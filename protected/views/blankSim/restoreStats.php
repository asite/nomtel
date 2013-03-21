<h1>Статистика по восстановлениям</h1>

<div class="cfix">
<?php $form=$this->beginWidget('BaseTbActiveForm',array(
    'id'=>'filter',
    'type'=>'horizontal'
)); ?>

<div class="form-container-horizontal">
    <div class="form-container-item form-label-width-60">
        <?php echo $form->maskFieldRow($model,'date_from','99.99.9999',array('class'=>'span2','errorOptions'=>array('hideErrorMessage'=>true))); ?>
    </div>
</div>

<div class="form-container-horizontal">
    <div class="form-container-item form-label-width-60">
        <?php echo $form->maskFieldRow($model,'date_to','99.99.9999',array('class'=>'span2','errorOptions'=>array('hideErrorMessage'=>true))); ?>
    </div>
</div>
<?php $this->endWidget(); ?>
</div>

<script>
    $('#filter').submit(function(){
        var data=$(this).serialize().replace(/YII_CSRF_TOKEN[^&]*/,'');
        $.fn.yiiGridView.update('stats-grid', {
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

<?php $this->widget('TbExtendedGridViewExport', array(
    'id' => 'stats-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'columns' => array(
        array(
            'name'=>'support_operator',
            'header'=>SupportOperator::label(),
            'value'=>'$data["surname"]." ".$data["name"]'
        ),
        array(
            'name'=>'cnt',
            'header'=>'Кол-во восстановлений',
            'htmlOptions'=>array('style'=>'text-align:center;')
        ),
    ),
)); ?>