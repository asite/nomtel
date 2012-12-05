<?php

$this->breadcrumbs = array(
  Yii::t('app','addDeliveryReport'),
);


?>

<script type="text/javascript">
  jQuery(document).ready(function(){
    $('.iconplus').live('click', function(){
      clone = $('#delivery').clone();
      index = $('#delivery_box').children('div').length + 1;
      clone.find('#ytDelivery_fileField_').attr('id','ytDelivery_fileField_'+index);
      clone.find('#Delivery_fileField_').attr('id','Delivery_fileField_'+index);
      clone.find('input[name="Delivery[fileField]"]').attr('name','Delivery[fileField]['+index+']');
      clone.find('label[for="Delivery_fileField_"]').attr('for','Delivery_fileField_'+index);

      clone.css({'display':'block'}).attr('id','').appendTo('#delivery_box');
      $(this).remove();
      return false;
    })
  })
</script>

<h1><?php echo Yii::t('app','addDeliveryReport'); ?></h1>

<div class="control-group cfix" style="display: none;" id="delivery">
  <label class="control-label" for="Delivery_fileField_">Добавте файл</label>
  <div class="controls">
    <input id="ytDelivery_fileField_" type="hidden" value="" name="Delivery[fileField]">
    <input name="Delivery[fileField]" id="Delivery_fileField_" type="file">
    <a href="#" class="iconplus"><i class="icon-plus"></i></a>
  </div>
</div>

<?php $form = $this->beginWidget('BaseTbActiveForm', array(
  'id' => 'add-delivery',
    'type' => 'horizontal',
    'enableAjaxValidation' => false,
        'clientOptions'=>array('validateOnSubmit' => false, 'validateOnChange' => false),
        'htmlOptions'=>array('enctype'=>'multipart/form-data')
));
?>

<div id="delivery_box">
  <div class="control-group cfix">
    <label class="control-label" for="Delivery_fileField_1">Добавте файл</label>
    <div class="controls">
      <input id="ytDelivery_fileField_1" type="hidden" value="" name="Delivery[fileField][1]">
      <input name="Delivery[fileField][1]" id="Delivery_fileField_1" type="file">
      <a href="#" class="iconplus"><i class="icon-plus"></i></a>
    </div>
  </div>
</div>

<?php echo CHtml::htmlButton('<i class="icon-ok icon-white"></i> '.Yii::t('app', 'Add'), array('class'=>'btn btn-primary', 'type'=>'submit')); ?>

<?php $this->endWidget(); ?>

<br/>
<?php

  if (Yii::app()->user->hasFlash('deliveryReport')) {
  $deliveryReport = unserialize(Yii::app()->user->getFlash('deliveryReport'));

  echo "<h2>Добавленые данные</h2>";

  $dataProvider = new CArrayDataProvider(
    $deliveryReport,
    array(
      'pagination'=>array(
        'pageSize'=>14,
      ),
    )
  );

  $this->widget('bootstrap.widgets.TbGridView', array(
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'columns' => array(
      'id',
      'personalAccount',
      'icc',
      'phoneNumber'
    ),
  )); }

 ?>

