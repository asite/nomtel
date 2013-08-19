<h1>Продажа номера '<?=$number->number?>'</h1>

<div class="form">

<?php $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'operator-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => false,
    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false),
));
?>

    <p class="note">
        <?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
    </p>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->radioButtonListRow($model,'type',CashierSellForm::getTypeList(),array('onchange'=>'toggleAgentId()')); ?>

    <div class="control-group" id="agent_id" <?php if($model->type!=CashierSellForm::TYPE_AGENT){ ?>style="display:none;"<?php }?> >
        <label for="CashierSellForm_agent_id" class="control-label required"><?php echo Yii::t('app','to Agent'); ?> <span class="required">*</span></label>
        <div class="controls">
    <?php
    $this->widget('bootstrap.widgets.TbSelect2', array(
        'name' => 'CashierSellForm[agent_id]',
        'id' => 'CashierSellForm_agent_id',
        'data' => Agent::getFullComboList(true,array(''=>'Пожалуйста выберите')),
        'val' => $model->agent_id,
        'options' => array(
            'width' => '400px',
        )
    ));
    ?>
        </div>
    </div>

    <?php echo $form->textFieldRow($model,'sum',array('class'=>'span2')); ?>

    <?php echo $form->radioButtonListRow($model,'payment',CashierSellForm::getPaymentList(),array('onchange'=>'toggleComment()')); ?>
    <div style="display:none" id="sum_id">
    <?php echo $form->textFieldRow($model,'comment',array('class'=>'span2')); ?>
    </div>

    <div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton',array(
        'buttonType'=>'submit',
        'label'=>'Продать'
    ));?>
    </div>
</div>

<?php $this->endWidget(); ?>
<script>
function toggleAgentId() {
    if ($("input[name='CashierSellForm[type]']:checked").val()==1) {
        $("#agent_id").show();
    } else {
        $("#agent_id").hide();
    }
}

function toggleComment() {
    if ($("input[name='CashierSellForm[payment]']:checked").val()==1) {
        $("#sum_id").show();
    } else {
        $("#sum_id").hide();
    }
}

$(function(){toggleAgentId();toggleComment();});
</script>