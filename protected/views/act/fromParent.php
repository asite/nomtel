<?php
  $this->breadcrumbs = array(
    Yii::t('app','moveSim'),
  );
?>

<script type="text/javascript">
$(document).ready(function(){$('#ICCtoSelect').focus();});
</script>

<h1>Сформировать акт приема SIM от вышестоящего агента или базы</h1>


<?php
  $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'move-sim',
    'enableAjaxValidation' => false,
    'clientOptions'=>array('validateOnSubmit' => false, 'validateOnChange' => false)
  ));
?>

<label>ICC номера / Номера телефона:</label>
<textarea name="ICCtoSelect" id="ICCtoSelect" rows="20" style="width:100%"></textarea>

<?php echo CHtml::htmlButton('<i class="icon-ok icon-white"></i> Принять', array('class'=>'btn btn-primary', 'style'=> 'float: right', 'type'=>'submit')); ?>

<?php $this->endWidget(); ?>