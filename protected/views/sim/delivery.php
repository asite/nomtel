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

<?php
  $this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true, // display a larger alert block?
    'fade'=>true, // use transitions?
    'closeText'=>'×', // close link text - if set to false, no close link is displayed
    'alerts'=>array( // configurations per alert type
      'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'×'), // success, info, warning, error or danger
      'error'=>array('block'=>true, 'fade'=>true, 'closeText'=>'×'), // success, info, warning, error or danger
    ),
  ));

?>

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

<div class="cfix">
  <label for="Delivery_operator" style="float: left; line-height: 30px; width: 160px; margin-right: 20px; text-align: right;"><?php echo Yii::t('app','Operator selection'); ?>:</label>
  <select name="Delivery[operator]" id="Delivery_operator">
    <option value="0">Выберете оператора...</option>
    <option value="1">Билайн</option>
    <option value="2">Мегафон</option>
  </select>
</div>
<br/><br/>

<?php echo CHtml::htmlButton('<i class="icon-ok icon-white"></i> '.Yii::t('app', 'AddDelivery'), array('class'=>'btn btn-primary', 'type'=>'submit')); ?>

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
        'id::ID',
        'personal_account::'.Yii::t('app','personal_account'),
        'icc::'.Yii::t('app','icc'),
        'number::'.Yii::t('app','number'),
      ),
    ));
  }

 ?>

