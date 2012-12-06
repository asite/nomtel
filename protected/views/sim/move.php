<?php
  $this->breadcrumbs = array(
    Yii::t('app','moveSim'),
  );
?>

<script type="text/javascript">
  jQuery(document).ready(function(){
    var countSim = <?php echo $dataProvider->itemCount ?>;
    $('#Move_totalCostSim').live('change', function(){
      $('#Move_PriceForSim').val($(this).val()/countSim);
    })
    $('#Move_PriceForSim').live('change', function(){
      $('#Move_totalCostSim').val($(this).val()*countSim);
    })

  })
</script>

<h1><?php echo Yii::t('app','moveSim'); ?></h1>

<?php
  $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'move-sim',
    'enableAjaxValidation' => false,
    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false)
  ));
?>

<?php
$this->widget('bootstrap.widgets.TbGridView', array(
  'itemsCssClass' => 'table table-striped table-bordered table-condensed',
  'dataProvider' => $dataProvider,
  'columns' => array(
    array(
      'name'=>'number',
      'sortable'=>false,
    ),
    array(
      'name'=>'icc',
      'value'=>'$data->shortIcc',
      'sortable'=>false,
    ),
    array(
      'name'=>'operator',
      'sortable'=>false,
    ),
    array(
      'name'=>'tariff',
      'sortable'=>false,
    ),
    array(
      'class' => 'bootstrap.widgets.TbEditableColumn',
      'name' => 'number_price',
      'sortable'=>false,
      'editable' => array(
        'url' => $this->createUrl("updatePrice", array("id"=>$data->id)),
        'placement' => 'right',
        'inputclass' => 'span3',
        'options' => array(
          'params' => array('YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
        ),
        'success'   => 'js: function(data) {
          alert(data.count);
        }'
      ),
      'htmlOptions' => array('style'=>'text-align:center;'),
    ),
    array(
      'class' => 'bootstrap.widgets.TbDataColumn',
      'header' => Yii::t('app','Sim Price'),
      'value' => 'Yii::app()->params->simPrice',
      'sortable'=>false,
      'htmlOptions' => array('style'=>'text-align:center;', 'data-info' => 'sim-price'),
    ),
    array(
      'class' => 'bootstrap.widgets.TbButtonColumn',
      'header' => Yii::t('app','Oparation'),
      'template'=>'{delete}',
      'afterDelete'=>'function(link,success,data){ if(data) alert(data); }',
      'buttons'=>array(
        'params' => array('YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
        'delete' => array(
          'label'=>Yii::t('app','Remove from list'),
          'imageUrl'=>'../img/glyphicons-halflings.png',
          'url'=>'Yii::app()->createUrl("sim/remove", array("id"=>$data->id,"key" => $_GET["key"]))',
        ),
      ),
    ),
  ),
));

?>

<h2><?php echo Yii::t('app','options'); ?></h2>

<div class="control-group left-label cfix">
  <label class="control-label" for="Move_totalCostSim"><?php echo Yii::t('app','total cost simcard'); ?></label>
  <div class="controls"><input class="width100" name="Move[totalCostSim]" id="Move_totalCostSim" type="text" value="<?php echo $dataProvider->itemCount*Yii::app()->params->simPrice; ?>"></div>
  <label class="control-label" for="Move_PriceForSim"><?php echo Yii::t('app','price for one sim'); ?></label>
  <div class="controls"><input class="width70" name="Move[PriceForSim]" id="Move_PriceForSim" type="text" value="<?php echo Yii::app()->params->simPrice; ?>"></div>
</div>

<?php $this->endWidget(); ?>