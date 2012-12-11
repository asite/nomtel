<?php
  $this->breadcrumbs = array(
    Yii::t('app','moveSim'),
  );
?>

<script type="text/javascript">
  var countSim = <?php echo $dataProvider->getTotalItemCount() ?>;
  var totalNumberPrice = <?php echo $totalNumberPrice ?>;

  function newPrice(priceN, countS) {
    countS = countS || countSim;
    if (countSim != countS) {
      countSim = countS;
      jQuery('#Move_totalCostSim').val(countSim * jQuery('#Move_PriceForSim').val());
    }
    priseS = $('#Move_totalCostSim').val();
    jQuery('#totalNumberPrice').html(Number(priceN)+Number(priseS));
  }

  function renderGridPriceSim(priceS) {
    priceS = priceS || jQuery('#Move_PriceForSim').val();
    jQuery('[data-info="sim-price"]').html(priceS);
  }

  jQuery(document).ready(function(){
    jQuery('#Move_totalCostSim').live('change', function(){
      jQuery('#Move_PriceForSim').val(jQuery(this).val()/countSim);
      renderGridPriceSim(jQuery('#Move_PriceForSim').val());
      newPrice(totalNumberPrice);
    })
    jQuery('#Move_PriceForSim').live('change', function(){
      jQuery('#Move_totalCostSim').val(jQuery(this).val()*countSim);
      renderGridPriceSim(jQuery(this).val());
      newPrice(totalNumberPrice);
    })
  })
</script>

<h1><?php echo Yii::t('app','moveSim'); ?></h1>

<?php

  $this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true, // display a larger alert block?
    'fade'=>true, // use transitions?
    'closeText'=>'×', // close link text - if set to false, no close link is displayed
    'alerts'=>array( // configurations per alert type
      'error'=>array('block'=>true, 'fade'=>true, 'closeText'=>'×'), // success, info, warning, error or danger
    ),
  ));

?>

<?php $date = time(); ?>
<h3 style="margin-bottom: 0; padding-bottom: 0;"><?php echo Yii::t('app','Act sims move'); ?> <span><?php echo date('d.m.Y - H:i:s', $date) ?></span></h3>

<?php
  $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'move-sim',
    'enableAjaxValidation' => false,
    'clientOptions'=>array('validateOnSubmit' => false, 'validateOnChange' => false)
  ));
?>

<input type="hidden" name="Move[date]" value="<?php echo $date; ?>">

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
        'url' => $this->createUrl("updatePrice", array("id"=>$data->id,'key' => $_GET['key'])),
        'placement' => 'right',
        'inputclass' => 'span3',
        'options' => array(
          'params' => array('YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
        ),
        'success'   => 'js: function(data) {
          newPrice(data.price);
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
      'afterDelete'=>'
        function(link,success,data){
          if(success) {
            data = JSON.parse(data);
            newPrice(data.price, data.count);
          }
        }',
      'buttons'=>array(
        'params' => array('YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
        'delete' => array(
          'label'=>Yii::t('app','remove from transmit list'),
          'imageUrl'=>'../img/glyphicons-halflings.png',
          'url'=>'Yii::app()->createUrl("sim/remove", array("id"=>$data->id,"key"=>$_GET["key"]))',
        ),
      ),
    ),
  ),
  'afterAjaxUpdate'=>'function(id, data){renderGridPriceSim();}',
));

?>

<h2><?php echo Yii::t('app','options'); ?></h2>

<div class="control-group left-label cfix">
  <label class="control-label" for="Move_totalCostSim"><?php echo Yii::t('app','total cost simcard'); ?></label>
  <div class="controls"><input class="width100" name="Move[totalCostSim]" id="Move_totalCostSim" type="text" value="<?php echo $dataProvider->getTotalItemCount()*Yii::app()->params->simPrice; ?>"></div>
  <label class="control-label" for="Move_PriceForSim"><?php echo Yii::t('app','price for one sim'); ?></label>
  <div class="controls"><input class="width70" name="Move[PriceForSim]" id="Move_PriceForSim" type="text" value="<?php echo Yii::app()->params->simPrice; ?>"></div>
</div>
<div class="control-group left-label cfix">
  <label class="control-label" for="Move_agent_id"><?php echo Yii::t('app','to Agent'); ?></label>
  <div class="controls">
    <?php
      $this->widget('bootstrap.widgets.TbSelect2', array(
        'name' => 'Move[agent_id]',
        'data' => $agent,
        'options' => array(
          'placeholder' => 'better',
          'width' => '400px',
        )
      ));
    ?>
  </div>
</div>


<div class="total_items_price" >
  ИТОГО
  <span id="totalNumberPrice"><?php echo ($totalNumberPrice + $dataProvider->getTotalItemCount()*Yii::app()->params->simPrice); ?></span>
  руб.
</div>
<?php echo CHtml::htmlButton('<i class="icon-ok icon-white"></i> '.Yii::t('app', 'moveSim'), array('class'=>'btn btn-primary', 'style'=> 'float: right', 'type'=>'submit')); ?>

<?php $this->endWidget(); ?>