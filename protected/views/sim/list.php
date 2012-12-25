<?php

$this->breadcrumbs = array(
    Yii::t('app', 'List'),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('sim-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo GxHtml::encode($model->label(2)); ?></h1>

<a class="btn" style="margin-bottom:10px;" href="#" onclick="jQuery('#Sim_number').val('<?php echo Yii::t('app','WITHOUT NUMBER');?>').trigger(jQuery.Event('keydown', {keyCode: 13}));"><?php echo Yii::t('app', 'Without number') ?></a>

<?php $this->widget('bootstrap.widgets.TbButton', array(
    'type'=>'button',
    'label'=>Yii::t('app','Pass selected SIM to agent'),
    'htmlOptions'=>array(
        'onclick'=>'passSIM();return false;',
        'style'=>"margin-bottom:10px;"
    )
)) ?>

<style>
    #sim-grid tfoot {
        display:none;
    }
</style>
<?php $this->widget('bootstrap.widgets.TbExtendedGridView', array(
    'id' => 'sim-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter' => $model,
    'afterAjaxUpdate' => 'js:function(id,data){multiPageSelRestore(id)}',
    'bulkActions' => array(
        'actionButtons' => array(
        ),
        'checkBoxColumnConfig' => array(
            'name' => 'id',
            'disabled' => '$data->agent_id ? true:false'
        ),
    ),
    'columns' => array(
        array(
            'name'=>'act_id',
            'value'=>'$data->act->dt ? $data->act->dt->format("d.m.Y"):"";    ',
            'filter'=>false,
        ),
        array(
            'name'=>'agent_id',
            'value'=>'GxHtml::valueEx($data->agent)',
            'filter'=>Agent::getComboList(array(0=>Yii::t('app','WITHOUT AGENT'))),
        ),
        array(
            'name'=>'number',
        ),
        array(
            'name'=>'icc',
        ),
        array(
            'name'=>'operator_id',
            'value'=>'$data->operator',
            'filter'=>Operator::getComboList(),
        ),
        array(
            'name'=>'tariff_id',
            'value'=>'$data->tariff',
            'filter'=>Tariff::getComboList(),
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'htmlOptions' => array('style'=>'width:40px;text-align:center;vertical-align:middle'),
            'template'=>'{feedback}',
            'buttons'=>array(
                'feedback'=>array(
                    'label'=>Yii::t('app','Report problem'),
                    'icon'=>'envelope',
                    'url'=>'Yii::app()->controller->createUrl("act/report",array("id"=>$data->id))',
                    'visible'=>'!isAdmin()'
                )
            )
        ),
    ),
)); ?>

<script>
    var multiPageSel={};

    function multiPageSelInit(id) {
        $(":checkbox").attr("autocomplete", "off");
        multiPageSel[id]={};
        jQuery(document).on('change click','input[name="'+id+'_c0[]"]',function(){
            if (this.checked) {
                multiPageSel[id][this.value]=true;
            } else {
                delete multiPageSel[id][this.value];
            }
        });
        jQuery(document).on('click','input[name="'+id+'_c0_all"]',function(){
            var checked=this.checked;
            jQuery('input[name="'+id+'_c0[]"]:enabled').each(function(){
                if (checked) {
                    multiPageSel[id][this.value]=true;
                } else {
                    delete multiPageSel[id][this.value];
                }
            });
        });
    }

    function multiPageSelRestore(id) {
        jQuery('input[name="'+id+'_c0[]"]').each(function() {
            this.checked=multiPageSel[id][this.value] ? true:false;
        });
    }

    multiPageSelInit('sim-grid');
</script>

<form method="post" action="?passSIM" id="ids-form">
    <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>">
    <input type="hidden" id="ids-input" name="ids" value="">
</form>
<script>
    function passSIM() {
        var ids='';
        for(var id in multiPageSel['sim-grid']) {
            if (ids!='') ids+=',';
            ids+=id;
        }
        jQuery('#ids-input').val(ids);
        jQuery('#ids-form').submit();
    }
</script>
