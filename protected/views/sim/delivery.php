<?php

$this->breadcrumbs = array(
  Yii::t('app','addAct'),
);


?>

<script type="text/javascript">
  jQuery(document).ready(function(){
    $('.iconplus').live('click', function(){
      clone = $('#delivery').clone();
      index = $(this).parents('.delivery_box').children('div').length + 1;
      clone.find('#ytDelivery_fileField_').attr('id','ytDelivery_fileField_'+index);
      clone.find('#Delivery_fileField_').attr('id','Delivery_fileField_'+index);
      clone.find('input[name="Delivery[fileField]"]').attr('name','Delivery[fileField]['+index+']');
      clone.find('label[for="Delivery_fileField_"]').attr('for','Delivery_fileField_'+index);

      clone.css({'display':'block'}).attr('id','').appendTo($(this).parents('.delivery_box'));
      $(this).remove();
      return false;
    })
  })
</script>

<h1><?php echo Yii::t('app','addAct'); ?></h1>

<div class="control-group cfix" style="display: none;" id="delivery">
  <label class="control-label" for="Delivery_fileField_">Добавте файл</label>
  <div class="controls">
    <input id="ytDelivery_fileField_" type="hidden" value="" name="Delivery[fileField]">
    <input name="Delivery[fileField]" id="Delivery_fileField_" type="file">
    <a href="#" class="iconplus"><i class="icon-plus"></i></a>
  </div>
</div>

<?php
  if (Yii::app()->user->hasFlash('activeTabs')) $activeTabs = unserialize(Yii::app()->user->getFlash('activeTabs'));
?>

<?php ob_start(); ?>

<?php $form = $this->beginWidget('BaseTbActiveForm', array(
  'id' => 'add-bilain',
    'type' => 'horizontal',
    'enableAjaxValidation' => true,
        'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false),
        'htmlOptions'=>array('enctype'=>'multipart/form-data')
));
?>

<?php echo $form->errorSummary($model); ?>

<input type="hidden" name="Sim[operator_id]" value="1">
<input type="hidden" name="simAdd[method]" value="add-bilain"/>

<div class="delivery_box">
  <div class="control-group cfix">
    <label class="control-label" for="Delivery_fileField_1">Добавте файл</label>
    <div class="controls">
      <input id="ytDelivery_fileField_1" type="hidden" value="" name="Delivery[fileField][1]">
      <input name="Delivery[fileField][1]" id="Delivery_fileField_1" type="file">
      <a href="#" class="iconplus"><i class="icon-plus"></i></a>
    </div>
  </div>
</div>

<div>
  <?php echo $form->dropDownListRow($model, 'company_id', $company); ?>
</div>
<div>
  <?php echo $form->dropDownListRow($model, 'operator_region_id', $regionList[1]); ?>
</div>
<br/><br/>

<?php echo CHtml::htmlButton('<i class="icon-ok icon-white"></i> '.Yii::t('app', 'AddDelivery'), array('class'=>'btn btn-primary', 'type'=>'submit')); ?>

<?php $this->endWidget(); ?>

<br/>
<?php

  if (Yii::app()->user->hasFlash('act') && $activeTabs['tab1']) {
    $act = unserialize(Yii::app()->user->getFlash('act'));

    echo "<h2>Добавленые данные</h2>";

    $dataProvider = new CArrayDataProvider(
      $act,
      array(
        'pagination'=>false
      )
    );

    $this->widget('bootstrap.widgets.TbGridView', array(
      'dataProvider' => $dataProvider,
      'itemsCssClass' => 'table table-striped table-bordered table-condensed',
      'columns' => array(
        'id::ID',
        'personal_account::'.Yii::t('app','personal_account'),
        'icc::'.Yii::t('app','icc'),
        'number::'.Yii::t('app','number'),
      ),
    ));
  }

 ?>

<?php $tab1 = ob_get_contents();  ob_end_clean(); ?>

<?php ob_start(); ?>
<div class="delivery_help">Формат ввода: 1 столбец - личный номер; 2 столбец - номер телефона; 3 столбец - ICC</div>
<?php $form = $this->beginWidget('BaseTbActiveForm', array(
  'id' => 'add-megafon',
    'type' => 'horizontal',
    'enableAjaxValidation' => true,
        'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false),
        'htmlOptions'=>array('enctype'=>'multipart/form-data')
));
?>

<?php echo $form->errorSummary($model); ?>

<input type="hidden" name="Sim[operator_id]" value="2">
<input type="hidden" name="simAdd[method]" value="add-megafon"/>

<div class="delivery_box">
  <div class="control-group cfix">
    <label class="control-label" for="Delivery_fileField_1">Добавте файл</label>
    <div class="controls">
      <input id="ytDelivery_fileField_1" type="hidden" value="" name="Delivery[fileField][1]">
      <input name="Delivery[fileField][1]" id="Delivery_fileField_1" type="file">
      <a href="#" class="iconplus"><i class="icon-plus"></i></a>
    </div>
  </div>
</div>

<div>
  <?php echo $form->dropDownListRow($model, 'company_id', $company); ?>
</div>
<div>
  <?php echo $form->dropDownListRow($model, 'operator_region_id', $regionList[2]); ?>
</div>
<div>
  <?php echo $form->dropDownListRow($model, 'tariff_id', $tariffList[2]); ?>
</div>
<div>
  <div>
    <div class="control-group ">
      <label class="control-label" for="Sim_agent_id">Куда передать карты ?</label>
      <div class="controls">
        <select name="Sim[agent_id]" id="Sim_agent_id">
          <option value="0">БАЗА</option>
          <option value="1">АГЕНТ</option>
        </select>
      </div>
    </div>
  </div>
</div>
<br/><br/>

<?php echo CHtml::htmlButton('<i class="icon-ok icon-white"></i> '.Yii::t('app', 'AddDelivery'), array('class'=>'btn btn-primary', 'type'=>'submit')); ?>

<?php $this->endWidget(); ?>

<br/>
<?php

  if (Yii::app()->user->hasFlash('act') && $activeTabs['tab2']) {
    $act = unserialize(Yii::app()->user->getFlash('act'));

    echo "<h2>Добавленые данные</h2>";

    $dataProvider = new CArrayDataProvider(
      $act,
      array(
        'pagination'=>false
      )
    );

    $this->widget('bootstrap.widgets.TbGridView', array(
      'dataProvider' => $dataProvider,
      'itemsCssClass' => 'table table-striped table-bordered table-condensed',
      'columns' => array(
        'id::ID',
        'personal_account::'.Yii::t('app','personal_account'),
        'icc::'.Yii::t('app','icc'),
        'number::'.Yii::t('app','number'),
      ),
    ));
  }

 ?>

<?php $tab2 = ob_get_contents();  ob_end_clean(); ?>


<?php

  $tabs=array(array('label'=>Yii::t('app', 'Bilain'), 'content'=>$tab1, 'active'=>$activeTabs['tab1']));
  if (!isKrylow()) {
    $tabs[]=array('label'=>Yii::t('app', 'Megafon'), 'content'=>$tab2, 'active'=>$activeTabs['tab2']);
  }

  $this->widget('bootstrap.widgets.TbTabs', array(
    'type'=>'tabs', // 'tabs' or 'pills'
    'tabs'=>$tabs
  ));

?>