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
<?php echo $form->errorSummary($status); ?>

    <div class="form-container-horizontal">
        <div class="form-container-item form-label-width-160">
            <?php echo $form->textFieldRow($number,'number',array('class'=>'span2','errorOptions'=>array('hideErrorMessage'=>true))); ?>
        </div>
        <div class="form-container-item form-label-width-160">
            &nbsp;<?php $d=$this->widget('bootstrap.widgets.TbButton',array('label'=>'Найти','buttonType'=>'submit','htmlOptions'=>array('name'=>'findNumber'))); ?>
        </div>
    </div>

    <div style="clear:both"></div>
<?php if ($showStatusForm) { ?>
    <?php
     echo $form->radioButtonListRow($status, 'status', Number::getSupportStatusDropDownList(),array(
         'onchange'=>'if (this.value=="CALLBACK") $("#callback").show();else $("#callback").hide();'));
    ?>

    <div id="callback" <?php if ($status->status!='CALLBACK') echo 'style="display:none;"';?>>
        <?php echo $form->PickerDateTimeRow($status,'callback_dt',array('class'=>'span2')); ?>
        <?php echo $form->textFieldRow($status,'callback_name',array('class'=>'span3')); ?>
    </div>

    <div class="form-actions">
    <?php $d=$this->widget('bootstrap.widgets.TbButton',array('label'=>Yii::t('app','Save'),'buttonType'=>'submit','type'=>'primary','icon'=>'ok white')); ?>
    </div>

<?php } ?>
<?php
$this->endWidget();
?>
