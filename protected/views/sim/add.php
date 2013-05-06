<?php

$this->breadcrumbs = array(
  Yii::t('app','addSim'),
);


?>

<h1><?php echo Yii::t('app','addSim'); ?></h1>

<script type="text/javascript">

  function changeOperator(mode,tariff) {
    $.ajax({
      type: "POST",
      url: "<?php echo $this->createUrl('ajaxcombo') ?>",
      data: { YII_CSRF_TOKEN: $('[name="YII_CSRF_TOKEN"]').val(), operatorId: $(mode).val() }
    }).done(function( msg ) {
      $(mode).siblings('select[name*="[tariff]"]').html(msg);
    });
      $.ajax({
          type: "POST",
          url: "<?php echo $this->createUrl('ajaxcombo2') ?>",
          data: { YII_CSRF_TOKEN: $('[name="YII_CSRF_TOKEN"]').val(), operatorId: $(mode).val() }
      }).done(function( msg ) {
                  $(mode).siblings('select[name*="[region]"]').html(msg);
              });
  }

  jQuery(document).ready(function(){
    $('.iconplussim').live('click',function(){
      clone = $('#addFewSims').clone();
      index = $('#boxSims').children('div').length;
      clone.find('input').each(function(){
        //$(this).attr('id',$(this).attr('id')+index);
        //AddNewSim[index][ICCPersonalAccount]
        $(this).attr('name',$(this).attr('name').replace(/index/,index));
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

  <?php if (!isKrylow()) echo $form->dropDownListRow($model, 'where', $whereListArray); ?>
</div>

<?php echo CHtml::htmlButton(Yii::t('app', 'buttonAddSim'), array('class'=>'btn btn-primary', 'name'=>'buttonAddSim', 'type'=>'submit')); ?>

<?php $this->endWidget(); ?>

<?php

  if (isset($actMany)) {

    if (empty($actMany)) $actMany = array();

    $dataProvider = array();
    foreach($actMany as $k=>$v) {
      $dataProvider[$k]['personal_account'] = $v->personal_account;
      $dataProvider[$k]['icc'] = $v->icc;
      $dataProvider[$k]['number'] = $v->number;
    }

    $dataProvider = new CArrayDataProvider(
      $dataProvider,
      array(
        'pagination'=>false
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
      <?php echo $form->textFieldRow($model,'[0]ICCPersonalAccount',array('errorOptions'=>array('hideErrorMessage'=>true),'class'=>'span2')); ?>
    </div>
    <div style="float: left; margin-right: 5px;">
      <?php echo $form->textFieldRow($model,'[0]ICCBeginFew',array('errorOptions'=>array('hideErrorMessage'=>true),'class'=>'span2')); ?>
    </div>
    <div style="float: left; margin-right: 5px;">
      <?php echo $form->textFieldRow($model,'[0]ICCEndFew',array('errorOptions'=>array('hideErrorMessage'=>true),'class'=>'span1')); ?>
    </div>
    <div style="float: left; margin-right: 5px;">
      <?php echo $form->textFieldRow($model,'[0]phone',array('errorOptions'=>array('hideErrorMessage'=>true),'class'=>'span2')); ?>
    </div>
    <?php if (!isset($_POST['AddNewSim']['ICCBeginFew'])): ?> <a href="#" class="iconplussim"><i class="icon-plus"></i></a><?php endif; ?>
  </div>
  <?php $count=count($_POST['AddNewSim']['ICCBeginFew']); for($k=1;$k<=$count;$k++): ?>
    <div class="cfix" style="position: relative;">
      <!--<div style="float: left; margin-right: 5px;"><input name="AddNewSim[ICCPersonalAccount]" type="text"></div>-->
      <div style="float: left; margin-right: 5px;"><input name="AddNewSim[<?php echo $k ?>][ICCBeginFew]" type="text" maxlength="20" value="<?php echo $_POST['AddNewSim'][$k]['ICCBeginFew'] ?>"></div>
      <div style="float: left; margin-right: 5px;"><input name="AddNewSim[<?php echo $k ?>][ICCEndFew]" type="text" maxlength="3" value="<?php echo $_POST['AddNewSim'][$k]['ICCEndFew'] ?>"></div>
      <!--<div style="float: left; margin-right: 5px;"><input name="AddNewSim[phone]" type="text"></div>-->
      <?php if ($k==$count): ?><a href="#" class="iconplussim"><i class="icon-plus"></i></a><?php endif; ?>
    </div>
  <?php endfor; ?>
</div>

<br/>
<div class="cfix">
  <?php echo $form->dropDownListRow($model, '[0]company', Company::getDropDownList()); ?>

  <?php echo $form->dropDownListRow($model, '[0]operator', $opListArray, array('onchange'=>'changeOperator(this);')); ?>

    <?php $regions=OperatorRegion::getDropDownList();
          $key=$model->operator ? $model->operator:key($opListArray);
          if (!isset($regions[$key])) $regions[$key]=array('' => 'Выбор региона');
    ?>

    <?php echo $form->dropDownListRow($model, '[0]region', $regions[$key]); ?>

  <?php echo $form->dropDownListRow($model, '[0]tariff', $tariffListArray); ?>

  <?php if (!isKrylow()) echo $form->dropDownListRow($model, '[0]where', $whereListArray); ?>
</div>

<?php echo CHtml::htmlButton(Yii::t('app', 'buttonAddSim'), array('class'=>'btn btn-primary', 'name'=>'buttonAddSim', 'type'=>'submit')); ?>

<?php $this->endWidget(); ?>

<?php
if (isset($actFew)) {

    $dataProvider = array();

    foreach($actFew as $k=>$v) {
      $dataProvider[$k]['personal_account'] = $v->personal_account;
      $dataProvider[$k]['icc'] = $v->icc;
      $dataProvider[$k]['number'] = $v->number;
    }

    $dataProvider = new CArrayDataProvider(
      $dataProvider,
      array(
        'pagination'=>false
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





<?php ob_start(); ?>

<?php
$form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'add-few-sim2',
    'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false)
));
?>
<input type="hidden" name="simMethod" value="add-few-sim2"/>

<?php echo $form->errorSummary($addSimByNumbers); ?>

<br/>
<div class="cfix">
    <?php echo $form->textAreaRow($addSimByNumbers, 'numbers', array('rows'=>20)); ?>

    <?php echo $form->dropDownListRow($addSimByNumbers, 'company', Company::getDropDownList()); ?>

    <?php echo $form->dropDownListRow($addSimByNumbers, 'operator', $opListArray, array('onchange'=>'changeOperator(this);')); ?>

    <?php $regions=OperatorRegion::getDropDownList();
    $key=$addSimByNumbers->operator ? $addSimByNumbers->operator:key($opListArray);
    if (!isset($regions[$key])) $regions[$key]=array('' => 'Выбор региона');
    ?>

    <?php echo $form->dropDownListRow($addSimByNumbers, 'region', $regions[$key]); ?>

    <?php echo $form->dropDownListRow($addSimByNumbers, 'tariff', $tariffListArray); ?>


    <?php if (!isKrylow()) echo $form->dropDownListRow($addSimByNumbers, 'where', $whereListArray); ?>
</div>

<?php echo CHtml::htmlButton(Yii::t('app', 'buttonAddSim'), array('class'=>'btn btn-primary', 'name'=>'buttonAddSim', 'type'=>'submit')); ?>

<?php $this->endWidget(); ?>

<?php $tab3 = ob_get_contents(); ob_end_clean(); ?>






<?php
$this->widget('bootstrap.widgets.TbTabs', array(
  'type'=>'tabs', // 'tabs' or 'pills'
  'tabs'=>array(
    array('label'=>Yii::t('app', 'manySims'), 'content'=>$tab1, 'active'=>$activeTabs['tab1']),
    array('label'=>Yii::t('app', 'fewSims'), 'content'=>$tab2, 'active'=>$activeTabs['tab2']),
    array('label'=>Yii::t('app', 'fewSims2'), 'content'=>$tab3, 'active'=>$activeTabs['tab3']),
   )
));

?>


<div class="cfix" id="addFewSims" style="display: none; position: relative;">
  <div style="float: left; margin-right: 5px;"><input class="span2" name="AddSim[index][ICCPersonalAccount]" type="text"></div>
  <div style="float: left; margin-right: 5px;"><input class="span2" name="AddSim[index][ICCBeginFew]" type="text" maxlength="20"></div>
  <div style="float: left; margin-right: 5px;"><input class="span1" name="AddSim[index][ICCEndFew]" type="text" maxlength="3"></div>
  <div style="float: left; margin-right: 5px;"><input class="span2" name="AddSim[index][phone]" type="text"></div>
  <a href="#" class="iconplussim"><i class="icon-plus"></i></a>
</div>