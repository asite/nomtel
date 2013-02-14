<?php

$this->breadcrumbs = array(
    Yii::t('app', 'List'),
);

?>

<h1><?php echo GxHtml::encode(Sim::label(2)); ?></h1>

<a class="btn" style="margin-bottom:10px;" href="#" onclick="jQuery('#SimSearch_number').val('<?php echo Yii::t('app','WITHOUT NUMBER');?>').trigger(jQuery.Event('change'));"><?php echo Yii::t('app', 'Without number') ?></a>

<?php $this->widget('bootstrap.widgets.TbButton', array(
    'type'=>'button',
    'label'=>Yii::t('app','Pass selected SIM to agent'),
    'htmlOptions'=>array(
        'onclick'=>'passSIM();return false;',
        'style'=>"margin-bottom:10px;"
    )
)) ?>


<?php $this->widget('bootstrap.widgets.TbButtonGroup', array(
    'toggle' => 'radio',
    'buttons' => array(
        array(
            'label'=>Yii::t('app','All'),
            'htmlOptions'=>array(
                'onclick'=>"jQuery('#SimSearch_operator_id').val('').trigger(jQuery.Event('change'))",
                'data-id'=>'',
                'style'=>"margin-bottom:10px;"
            )
        ),
        array(
            'label'=>Yii::t('app','Bilain'),
            'htmlOptions'=>array(
                'onclick'=>"jQuery('#SimSearch_operator_id').val(".Operator::OPERATOR_BEELINE_ID.").trigger(jQuery.Event('change'))",
                'data-id'=>Operator::OPERATOR_BEELINE_ID,
                'style'=>"margin-bottom:10px;"
            )
        ),
        array(
            'label'=>Yii::t('app','Megafon'),
            'htmlOptions'=>array(
                'onclick'=>"jQuery('#SimSearch_operator_id').val(".Operator::OPERATOR_MEGAFON_ID.").trigger(jQuery.Event('change'))",
                'data-id'=>Operator::OPERATOR_MEGAFON_ID,
                'style'=>"margin-bottom:10px;"
            )
            ),
    ),
)); ?>

<?php $this->widget('bootstrap.widgets.TbButton', array(
    'type'=>'button',
    'label'=>Yii::t('app','Make Exel'),
    'htmlOptions'=>array(
        'onclick'=>'makeExel();return false;',
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
            'name' => 'sim_id',
            'disabled' => '$data["agent_id"] ? true:false'
        ),
    ),
    'columns' => array(
        array(
            'name'=>'agent_id',
            'value'=>'CHtml::encode($data["surname"]." ".$data["name"])',
            'filter'=>Agent::getComboList(array(0=>Yii::t('app','WITHOUT AGENT'))),
            'header'=>Agent::label(1)
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
            'name'=>'operator_id',
            'value'=>'$data["operator"]',
            'filter'=>Operator::getComboList(),
            'header'=>Operator::label(1),
            'htmlOptions'=>array(
                'onchange'=>'alert("ok")',
            )
        ),
        array(
            'name'=>'tariff_id',
            'value'=>'$data["tariff"]',
            'filter'=>Tariff::getComboList(),
            'header'=>Tariff::label(1),
        ),
        array(
            'name'=>'status',
            'value'=>'Number::getStatusLabel($data["status"])',
            'filter'=>Number::getStatusDropDownList(),
            'header'=>Yii::t('app','Status'),
        ),
        array(
            'name'=>'support_status',
            'value'=>'Number::getSupportStatusLabel($data["support_status"])',
            'filter'=>Number::getSupportStatusDropDownList(array('0'=>'БЕЗ СТАТУСА')),
            'header'=>Yii::t('app','Support Status'),
        ),
        array(
            'name'=>'support_operator_id',
            'value'=>'CHtml::encode($data["so_surname"]." ".$data["so_name"])',
            'filter'=>SupportOperator::getComboList(array(0=>'БЕЗ ОПЕРАТОРА')),
            'header'=>SupportOperator::label(),
        ),
        array(
            'name'=>'balance_status',
            'value'=>'Number::getBalanceStatusLabel($data["balance_status"])',
            'filter'=>Number::getBalanceStatusDropDownList(),
            'header'=>Yii::t('app','Balance Status'),
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'htmlOptions' => array('style'=>'width:40px;text-align:center;vertical-align:middle'),
            'template'=>'{view} {feedback} {createAgreement} {viewAgreement}',
            'buttons'=>array(
                'view'=>array(
                    'url'=>'Yii::app()->createUrl("number/".(Yii::app()->user->checkAccess("editNumberCard") ? "edit":"view"),array("id"=>$data["number_id"]))',
                ),

                'feedback'=>array(
                    'label'=>Yii::t('app','Report problem'),
                    'icon'=>'envelope',
                    'url'=>'Yii::app()->controller->createUrl("act/report",array("id"=>$data["sim_id"]))',
                    'visible'=>'!isAdmin()'
                ),
                'createAgreement'=>array(
                    'label'=>Yii::t('app','Create subscription agreement'),
                    'icon'=>'file',
                    'url'=>'Yii::app()->controller->createUrl("subscriptionAgreement/startCreate",array("sim_id"=>$data["sim_id"]))',
                    'visible'=>'Yii::app()->user->checkAccess("createSubscriptionAgreement",array("parent_agent_id"=>$data["parent_agent_id"],"number_status"=>$data["number_status"]))'
                ),
                'viewAgreement'=>array(
                    'label'=>Yii::t('app','Договор'),
                    'icon'=>'file',
                    'url'=>'Yii::app()->controller->createUrl("subscriptionAgreement/update",array("number_id"=>$data["number_id"]))',
                    'visible'=>'Yii::app()->user->checkAccess("updateSubscriptionAgreement",array("parent_agent_id"=>$data["parent_agent_id"],"number_status"=>$data["number_status"]))'
                ),
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
<form method="post" action="?exportExel" id="makeexel-form">
    <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>">
</form>
<script type="text/javascript">
    function makeExel() {
        $('#sim-grid').find('[name^="SimSearch"]').each(function(){
            $('#makeexel-form').append('<input type="hidden" name="'+$(this).attr('name')+'" value="'+$(this).val()+'">');
        })
        $('#makeexel-form').submit();
    }
</script>