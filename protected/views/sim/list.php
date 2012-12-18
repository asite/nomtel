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
    'filter' => $dataModel,
    'afterAjaxUpdate' => 'js:function(id,data){multiPageSelRestore(id)}',
    'bulkActions' => array(
        'actionButtons' => array(
        ),
        'checkBoxColumnConfig' => array(
            'name' => 'id',
        ),
    ),
    'columns' => array(
        array(
            'name'=>'delivery_report_dt',
            'value'=>'$data["delivery_report_dt"]!="" ? new EDateTime($data["delivery_report_dt"]):""',
            'header'=>DeliveryReport::model()->label(1),
            'filter'=>false,
        ),
        array(
            'name'=>'agent_name',
            'header'=>Agent::model()->label(1),
            'filter'=>array_merge(array(0=>Yii::t('app','WITHOUT AGENT')),Agent::getComboList()),
        ),
        array(
            'name'=>'number',
            'header'=>Yii::t('app','Number'),
        ),
        array(
            'name'=>'icc',
            'header'=>Yii::t('app','Icc'),
        ),
        array(
            'name'=>'operator',
            'filter'=>Operator::getComboList(),
            'header'=>Operator::model()->label(1),
        ),
        array(
            'name'=>'tariff',
            'filter'=>Tariff::getComboList(),
            'header'=>Tariff::model()->label(1),
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'htmlOptions' => array('style'=>'width:40px;text-align:center;vertical-align:middle'),
            'template'=>'{feedback}',
            'buttons'=>array(
                'feedback'=>array(
                        'label'=>Yii::t('app','Report problem'),
                        'icon'=>'envelope',
                        'url'=>'Yii::app()->controller->createUrl("deliveryReport/report",array("id"=>$data["id"]))'
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
