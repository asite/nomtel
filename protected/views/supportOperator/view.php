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
<input type="hidden" name="setnumbers" value="20"/>
<?php
echo CHtml::htmlButton('<i class="icon-ok"></i> '.Yii::t('app', 'Set Numbers'), array('class'=>'btn', 'type'=>'submit'));
$this->endWidget();
?>

<?php
$this->widget('TbExtendedGridViewExport', array(
  'id' => 'number-grid',
  'itemsCssClass' => 'table table-striped table-bordered table-condensed',
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
    array(
      'name'=>'support_operator_got_dt',
      'sortable'=>true,
      'value'=>'$data->support_operator_got_dt',
      'htmlOptions' => array('style'=>'text-align: center;vertical-align:middle'),
      'header'=>Yii::t('app','Getting Date')
    ),
    array(
      'name'=>'support_status',
      'sortable'=>true,
      'value'=>'Number::getSupportStatusLabel($data->support_status)',
      'filter'=>Number::getSupportStatusLabels(),
      'htmlOptions' => array('style'=>'text-align: center;vertical-align:middle'),
      'header'=>Yii::t('app','Status')
    ),
    array(
      'name'=>'support_sent_sms_status',
      'sortable'=>true,
      'value'=>'Number::getSupportSMSStatusLabel($data->support_sent_sms_status)',
      'filter'=>Number::getSupportSMSStatusLabels(),
      'htmlOptions' => array('style'=>'text-align: center;vertical-align:middle'),
      'header'=>Yii::t('app','SMS')
    ),
  ),
));

?>