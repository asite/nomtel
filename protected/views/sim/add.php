<?php

$this->breadcrumbs = array(
  Yii::t('app','addSim'),
);


?>

<h1><?php echo Yii::t('app','addSim'); ?></h1>


<?php ob_start(); ?>

<?php
  $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'add-much-sim',
    'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false)
  ));
?>

<input type="hidden" name="simMethod" value="add-much-sim"/>

<?php echo $form->errorSummary($model); ?>

<div class="cfix">
  <div style="float: left; margin-right: 5px;">
    <?php echo $form->textFieldRow($model,'ICCFirst',array('errorOptions'=>array('hideErrorMessage'=>true))); ?>
  </div>
  <div style="float: left; margin-right: 5px;">
    <?php echo $form->textFieldRow($model,'ICCBegin',array('errorOptions'=>array('hideErrorMessage'=>true))); ?>
  </div>
  <div style="float: left; margin-right: 5px;">
    <?php echo $form->textFieldRow($model,'ICCEnd',array('errorOptions'=>array('hideErrorMessage'=>true))); ?>
  </div>
</div>
<?php echo CHtml::htmlButton(Yii::t('app', 'buttonProcessSim'), array('class'=>'btn btn-primary','name'=>'buttonProcessSim', 'type'=>'submit')); ?>

<br/><br/>
<div class="cfix">
  <?php echo $form->dropDownListRow($model, 'operator', $opListArray); ?>

  <?php echo $form->dropDownListRow($model, 'tariff', $tariffListArray); ?>

  <?php echo $form->dropDownListRow($model, 'where', $whereListArray); ?>
</div>

<?php echo CHtml::htmlButton(Yii::t('app', 'buttonAddSim'), array('class'=>'btn btn-primary', 'name'=>'buttonAddSim', 'type'=>'submit')); ?>

<?php $this->endWidget(); ?>


<?php $tab1 = ob_get_contents();  ob_end_clean(); ?>

<?php ob_start(); ?>

<?php
  $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'add-few-sim',
    'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false)
  ));
?>
<input type="hidden" name="simMethod" value="add-few-sim"/>

<?php echo $form->errorSummary($model); ?>

<div class="cfix">
  <div style="float: left; margin-right: 5px;">
    <?php echo $form->textFieldRow($model,'ICCPersonalAccount',array('errorOptions'=>array('hideErrorMessage'=>true))); ?>
  </div>
  <div style="float: left; margin-right: 5px;">
    <?php echo $form->textFieldRow($model,'ICCBeginFew',array('errorOptions'=>array('hideErrorMessage'=>true))); ?>
  </div>
  <div style="float: left; margin-right: 5px;">
    <?php echo $form->textFieldRow($model,'ICCEndFew',array('errorOptions'=>array('hideErrorMessage'=>true))); ?>
  </div>
  <div style="float: left; margin-right: 5px;">
    <?php echo $form->textFieldRow($model,'phone',array('errorOptions'=>array('hideErrorMessage'=>true))); ?>
  </div>
</div>
<?php echo CHtml::htmlButton(Yii::t('app', 'buttonProcessSim'), array('class'=>'btn btn-primary','name'=>'buttonProcessSim', 'type'=>'submit')); ?>

<br/><br/>
<div class="cfix">
  <?php echo $form->dropDownListRow($model, 'operator', $opListArray); ?>

  <?php echo $form->dropDownListRow($model, 'tariff', $tariffListArray); ?>

  <?php echo $form->dropDownListRow($model, 'where', $whereListArray); ?>
</div>

<?php echo CHtml::htmlButton(Yii::t('app', 'buttonAddSim'), array('class'=>'btn btn-primary', 'name'=>'buttonAddSim', 'type'=>'submit')); ?>

<?php $this->endWidget(); ?>

<?php $tab2 = ob_get_contents(); ob_end_clean(); ?>

<?php
$this->widget('bootstrap.widgets.TbTabs', array(
  'type'=>'tabs', // 'tabs' or 'pills'
  'tabs'=>array(
    array('label'=>'Много симкарт', 'content'=>$tab1, 'active'=>true),
    array('label'=>'Несколько симкарт', 'content'=>$tab2),
  ),
));

?>