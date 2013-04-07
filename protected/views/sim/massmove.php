<?php
  $this->breadcrumbs = array(
    Yii::t('app','Mass move'),
  );
?>

<script type="text/javascript">
$(document).ready(function(){$('#ICCtoSelect').focus();});
</script>

<h1><?php echo Yii::t('app','Mass move') ?> SIM</h1>


<?php
  $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'move-sim',
    'enableAjaxValidation' => false,
    'clientOptions'=>array('validateOnSubmit' => false, 'validateOnChange' => false)
  ));
?>

<label>Введите список ICC:</label>
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