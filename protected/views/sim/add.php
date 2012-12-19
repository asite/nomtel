<?php

$this->breadcrumbs = array(
  Yii::t('app','addSim'),
);


?>

<h1><?php echo Yii::t('app','addSim'); ?></h1>

<script type="text/javascript">

  function changeOperator(mode) {
    $.ajax({
      type: "POST",
      url: "<?php echo $this->createUrl('ajaxcombo') ?>",
      data: { YII_CSRF_TOKEN: $('[name="YII_CSRF_TOKEN"]').val(), operatorId: $(mode).val() }
    }).done(function( msg ) {
      $(mode).siblings('[name="AddSim[tariff]"]').html(msg);
    });
  }

  jQuery(document).ready(function(){
    $('.iconplussim').live('click',function(){
      clone = $('#addFewSims').clone();
      index = $('#boxSims').children('div').length;
      clone.find('input').each(function(){
        //$(this).attr('id',$(this).attr('id')+index);
        $(this).attr('name',$(this).attr('name')+'['+index+']');
      })

      clone.css({'display':'block'}).attr('id','').appendTo('#boxSims');
      $(this).remove();
      return false;
    })
  })
</script>

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

  <?php echo $form->dropDownListRow($model, 'operator', $opListArray, array('onchange'=>'changeOperator(this);')); ?>

  <?php echo $form->dropDownListRow($model, 'tariff', $tariffListArray); ?>

  <?php echo $form->dropDownListRow($model, 'where', $whereListArray); ?>
</div>

<?php echo CHtml::htmlButton(Yii::t('app', 'buttonAddSim'), array('class'=>'btn btn-primary', 'name'=>'buttonAddSim', 'type'=>'submit')); ?>

<?php $this->endWidget(); ?>

<?php

  if (isset($deliveryReportMany)) {

    if (empty($deliveryReportMany)) $deliveryReportMany = array();

    $dataProvider = array();
    foreach($deliveryReportMany as $k=>$v) {
      $dataProvider[$k]['personal_account'] = $v->personal_account;
      $dataProvider[$k]['icc'] = $v->icc;
      $dataProvider[$k]['number'] = $v->number;
    }

    $dataProvider = new CArrayDataProvider(
      $dataProvider,
      array(
        'pagination'=>array(
          'pageSize'=>14,
        ),
      )
    );

    echo "<h2>".Yii::t('app', 'foundDataSim')."</h2>";

    $this->widget('bootstrap.widgets.TbGridView', array(
      'id' => 'sim-grid',
      'dataProvider' => $dataProvider,
      'itemsCssClass' => 'table table-striped table-bordered table-condensed',
      'columns' => array(
        'personal_account::'.Yii::t('app','personal_account'),
        'icc::'.Yii::t('app','icc'),
        'number::'.Yii::t('app','number'),
      )
     ));
  }
?>

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

<div id="boxSims">
  <div class="cfix" style="position: relative;">
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
    <?php if (!isset($_POST['AddNewSim']['ICCBeginFew'])): ?> <a href="#" class="iconplussim"><i class="icon-plus"></i></a><?php endif; ?>
  </div>
  <?php $count=count($_POST['AddNewSim']['ICCBeginFew']); for($k=1;$k<=$count;$k++): ?>
    <div class="cfix" style="position: relative;">
      <!--<div style="float: left; margin-right: 5px;"><input name="AddNewSim[ICCPersonalAccount]" type="text"></div>-->
      <div style="float: left; margin-right: 5px;"><input name="AddNewSim[ICCBeginFew][<?php echo $k ?>]" type="text" maxlength="15" value="<?php echo $_POST['AddNewSim']['ICCBeginFew'][$k] ?>"></div>
      <div style="float: left; margin-right: 5px;"><input name="AddNewSim[ICCEndFew][<?php echo $k ?>]" type="text" maxlength="3" value="<?php echo $_POST['AddNewSim']['ICCEndFew'][$k] ?>"></div>
      <!--<div style="float: left; margin-right: 5px;"><input name="AddNewSim[phone]" type="text"></div>-->
      <?php if ($k==$count): ?><a href="#" class="iconplussim"><i class="icon-plus"></i></a><?php endif; ?>
    </div>
  <?php endfor; ?>
</div>

<br/>
<div class="cfix">
  <?php echo $form->dropDownListRow($model, 'operator', $opListArray, array('onchange'=>'changeOperator(this);')); ?>

  <?php echo $form->dropDownListRow($model, 'tariff', $tariffListArray); ?>

  <?php echo $form->dropDownListRow($model, 'where', $whereListArray); ?>
</div>

<?php echo CHtml::htmlButton(Yii::t('app', 'buttonAddSim'), array('class'=>'btn btn-primary', 'name'=>'buttonAddSim', 'type'=>'submit')); ?>

<?php $this->endWidget(); ?>

<?php
if (isset($deliveryReportFew)) {

    $dataProvider = array();

    foreach($deliveryReportFew as $k=>$v) {
      $dataProvider[$k]['personal_account'] = $v->personal_account;
      $dataProvider[$k]['icc'] = $v->icc;
      $dataProvider[$k]['number'] = $v->number;
    }

    $dataProvider = new CArrayDataProvider(
      $dataProvider,
      array(
        'pagination'=>array(
          'pageSize'=>14,
        ),
      )
    );

    echo "<h2>".Yii::t('app', 'foundDataSim')."</h2>";

    $this->widget('bootstrap.widgets.TbGridView', array(
      'id' => 'sim-grid',
      'dataProvider' => $dataProvider,
      'itemsCssClass' => 'table table-striped table-bordered table-condensed',
      'columns' => array(
        'personal_account::'.Yii::t('app','personal_account'),
        'icc::'.Yii::t('app','icc'),
        'number::'.Yii::t('app','number'),
      )
     ));
  }
?>

<?php $tab2 = ob_get_contents(); ob_end_clean(); ?>

<?php
$this->widget('bootstrap.widgets.TbTabs', array(
  'type'=>'tabs', // 'tabs' or 'pills'
  'tabs'=>array(
    array('label'=>Yii::t('app', 'manySims'), 'content'=>$tab1, 'active'=>$activeTabs['tab1']),
    array('label'=>Yii::t('app', 'fewSims'), 'content'=>$tab2, 'active'=>$activeTabs['tab2']),
   )
));

?>


<div class="cfix" id="addFewSims" style="display: none; position: relative;">
  <div style="float: left; margin-right: 5px;"><input name="AddNewSim[ICCPersonalAccount]" type="text"></div>
  <div style="float: left; margin-right: 5px;"><input name="AddNewSim[ICCBeginFew]" type="text" maxlength="15"></div>
  <div style="float: left; margin-right: 5px;"><input name="AddNewSim[ICCEndFew]" type="text" maxlength="3"></div>
  <div style="float: left; margin-right: 5px;"><input name="AddNewSim[phone]" type="text"></div>
  <a href="#" class="iconplussim"><i class="icon-plus"></i></a>
</div>