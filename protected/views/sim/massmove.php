<?php
  $this->breadcrumbs = array(
    Yii::t('app','Mass move'),
  );
?>

<script type="text/javascript">
$(document).ready(function(){$('#ICCtoSelect').focus();});
</script>

<h1>Массовая форсированная передача SIM со сменой ветки агентов</h1>


<?php
  $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'move-sim',
    'enableAjaxValidation' => false,
    'clientOptions'=>array('validateOnSubmit' => false, 'validateOnChange' => false)
  ));
?>

<label>Введите список ICC/Номеров или диапазон ICC "89701020220147270042-89701020220147270052":</label>
<textarea name="ICCtoMove" id="ICCtoMove" rows="20" style="width:100%"></textarea>

    <?php
      $this->widget('bootstrap.widgets.TbSelect2', array(
        'name' => 'agent_id',
        'id' => 'agent_id',
        'data' => $agent,
        'options' => array(
          'width' => '400px',
        )
      ));
    ?>



<?php echo CHtml::htmlButton('<i class="icon-ok icon-white"></i> '.Yii::t('app', 'moveSim'), array('class'=>'btn btn-primary', 'style'=> 'float: right', 'type'=>'submit')); ?>

<?php $this->endWidget(); ?>