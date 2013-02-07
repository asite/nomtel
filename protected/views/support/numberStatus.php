<?php

$this->breadcrumbs = array(
    Number::label(2)
);
?>

<h1><?=Yii::t('app','Number')?></h1>

<?php $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'report-form',
    'type' => 'horizontal',
    //'htmlOptions' => array ('class'=>'well')
));
?>

<?php echo $form->errorSummary($number); ?>

    <div class="form-container-horizontal">
        <div class="form-container-item form-label-width-160">
            <?php echo $form->maskFieldRow($number,'number','8 (999) 999-99-99',array('class'=>'span2','errorOptions'=>array('hideErrorMessage'=>true))); ?>
        </div>
        <div class="form-container-item form-label-width-160">
            &nbsp;<?php $d=$this->widget('bootstrap.widgets.TbButton',array('label'=>'Найти','buttonType'=>'submit','htmlOptions'=>array('name'=>'findNumber'))); ?>
        </div>
    </div>

    <div style="clear:both"></div>
<?php if ($showStatusForm) { ?>
<script>
function statusProcessVisibleItems() {
    var status=$("#NumberSupportStatus_status").val();
    if (status=='') return;

    $("#callback, #active, .form-actions").hide();
    switch (status) {
        case 'CALLBACK':
            $("#callback, .form-actions").show();
            break;
        case 'ACTIVE':
            $("#active, .form-actions").show();
            break;
        default:
            $("#report-form").submit();
    }
}

function setStatus(status) {
    $("#NumberSupportStatus_status").val(status);
    statusProcessVisibleItems();
}
</script>
<?php
    if ($numberObj->status==Number::STATUS_FREE) {
        foreach(Number::getSupportStatusLabels() as $status_key=>$status_label) {
            $d=$this->widget('bootstrap.widgets.TbButton',array(
                'label'=>$status_label,
                'htmlOptions'=>array('onclick'=>'setStatus("'.$status_key.'")')
            ));
            echo "&nbsp;";
        }
    } else {
            $d=$this->widget('bootstrap.widgets.TbButton',array(
                'label'=>'Полная форма договора',
                'url'=>$this->createUrl('subscriptionAgreement/update',array('number_id'=>$numberObj->id))
            ));
            echo "&nbsp;";
            $d=$this->widget('bootstrap.widgets.TbButton',array(
                'label'=>Number::getSupportStatusLabel('SERVICE_INFO'),
                'htmlOptions'=>array('onclick'=>'setStatus("SERVICE_INFO")')
            ));
   }
?>


<?php echo $form->hiddenField($status, 'status') ?>

<div style="margin-top:20px;"></div>

<?php echo $form->errorSummary(array($status,$person)); ?>

<div id="callback" style="display:none;">
        <?php echo $form->PickerDateTimeRow($status,'callback_dt',array('class'=>'span2')); ?>
        <?php echo $form->textFieldRow($status,'callback_name',array('class'=>'span3')); ?>
    </div>

    <div id="active" style="display:none;">

            <div class="form-container-horizontal">
                <div class="form-container-item form-label-width-220">
                    <?php echo $form->textFieldRow($status,'getting_passport_variant',array('class'=>'span4')); ?>
                    <?php echo $form->textFieldRow($status,'number_region_usage',array('class'=>'span4')); ?>
                </div>
            </div>

        <fieldset>
            <legend><?php echo Yii::t('app','Subscriber');?></legend>
            <div class="form-container-horizontal">
                <div class="form-container-item form-label-width-140">
                    <?php echo $form->textFieldRow($person,'surname',array('class'=>'span2','maxlength'=>100,'errorOptions'=>array('hideErrorMessage'=>true))); ?>

                    <?php echo $form->textFieldRow($person,'name',array('class'=>'span2','maxlength'=>100,'errorOptions'=>array('hideErrorMessage'=>true))); ?>

                    <?php echo $form->textFieldRow($person,'middle_name',array('class'=>'span2','maxlength'=>100,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
                </div>
                <div class="form-container-item form-label-width-100">
                    <?php echo $form->textFieldRow($person,'phone',array('class'=>'span2','maxlength'=>50,'errorOptions'=>array('hideErrorMessage'=>true))); ?>

                    <?php echo $form->textFieldRow($person,'email',array('class'=>'span2','maxlength'=>50,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
                </div>
        </fieldset>

        <fieldset>
            <legend><?php echo Yii::t('app','Passport');?></legend>
            <div class="form-container-horizontal">
                <div class="form-container-item form-label-width-140">
                    <?php echo $form->textFieldRow($person,'passport_series',array('class'=>'span1','maxlength'=>10,'onchange'=>'checkPassport()','errorOptions'=>array('hideErrorMessage'=>true))); ?>
                </div>
                <div class="form-container-item form-label-width-80">
                    <?php echo $form->textFieldRow($person,'passport_number',array('class'=>'span2','maxlength'=>20,'onchange'=>'checkPassport()','errorOptions'=>array('hideErrorMessage'=>true))); ?>
                </div>
                <div class="form-container-item form-label-width-120">
                    <?php echo $form->pickerDateRow($person,'passport_issue_date',array('class'=>'span2','errorOptions'=>array('hideErrorMessage'=>true))); ?>
                </div>
            </div>

            <div class="form-container-horizontal">
                <div class="form-container-item form-label-width-140">
                    <?php echo $form->textFieldRow($person,'passport_issuer',array('class'=>'span7','maxlength'=>200,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
                </div>
            </div>

            <div class="form-container-horizontal">
                <div class="form-container-item form-label-width-140">
                    <?php echo $form->pickerDateRow($person,'birth_date',array('class'=>'span2','errorOptions'=>array('hideErrorMessage'=>true))); ?>
                </div>
                <div class="form-container-item form-label-width-140">
                    <?php echo $form->textFieldRow($person,'birth_place',array('class'=>'span3','maxlength'=>200,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
                </div>
            </div>

            <div class="form-container-horizontal">
                <div class="form-container-item form-label-width-140">
                    <?php echo $form->textFieldRow($person,'registration_address',array('class'=>'span7','maxlength'=>200,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
                </div>
            </div>
        </fieldset>


    </div>

    <div class="form-actions" style="display:none;">
    <?php $d=$this->widget('bootstrap.widgets.TbButton',array('label'=>Yii::t('app','Save'),'buttonType'=>'submit','type'=>'primary','icon'=>'ok white')); ?>
    </div>

<script>statusProcessVisibleItems();</script>
<?php } ?>
<?php
$this->endWidget();
?>
