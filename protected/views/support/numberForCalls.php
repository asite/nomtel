<?php

$this->breadcrumbs = array(
    Yii::t('app','Operator Numbers')
);
?>

<h1><?=Yii::t('app','Operator Numbers')?></h1>

<?php $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'get-numbers',
    'enableAjaxValidation' => false,
    'clientOptions'=>array('validateOnSubmit' => false, 'validateOnChange' => false),
));
?>
<input type="hidden" name="getnumbers" value="20"/>
<?php
echo CHtml::htmlButton('<i class="icon-ok"></i> '.Yii::t('app', 'Get Numbers'), array('class'=>'btn', 'type'=>'submit'));
$this->endWidget();
?>

<?php
$this->widget('bootstrap.widgets.TbGridView', array(
  'id' => 'number-grid',
  'itemsCssClass' => 'table table-striped table-bordered table-condensed',
  'htmlOptions' => array('style'=>'width: 50%;'),
  'dataProvider' => $dataProvider,
  'filter'=>$model,
  'columns' => array(
    array(
      'name'=>'number',
      'sortable'=>true,
      'value'=>'$data->number',
      'htmlOptions' => array('style'=>'text-align: center;vertical-align:middle'),
      'header'=>Yii::t('app','Number')
    ),
  ),
));

?>