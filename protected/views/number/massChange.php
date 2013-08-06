<?php

$this->breadcrumbs = array(
    Number::label(2)=>$this->createUrl('number/list'),
    Yii::t('app','Mass change')
);

?>

<h1><?php echo Yii::t('app','Mass change'); ?></h1>

<?php $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'mass-change-form',
    'type' => 'horizontal',
    'htmlOptions'=>array('enctype'=>'multipart/form-data')
));
?>

<div class="delivery_box">
  <div class="control-group cfix">
    <label class="control-label" for="MassChange_fileField">Добавте файл</label>
    <div class="controls">
      <input id="ytfileField" type="hidden" value="" name="fileField">
      <input name="fileField" id="fileField" type="file">
      <button name="action" value="downloadFile" class="btn" type="submit"  style="margin-left: 20px;"><?php echo Yii::t('app','Download'); ?></button>
    </div>
  </div>
</div>

<?php $this->endWidget(); ?>

<?php $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'mass-change-form',
    'type' => 'horizontal'
));
?>
<?php if ($file): ?>
<input type="hidden" name="MassChange[Action]" value="<?php echo $head; ?>">
<table class="table table-striped table-bordered table-condensed table">
    <thead>
        <tr>
            <th><?php echo Yii::t('app', $head); ?></th>
            <th>Действие</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($file as $key=>$value): ?>
            <?php
                $csv[$value[0]] = $value[1];
            ?>
            <tr class="odd">
                <td class="<?php $key%2?'odd':'even'; ?>">
                    <?php echo $value[0] ?>
                </td>
                <td>
                    <?php echo $value[1] ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
<input type="hidden" name="Csv" value='<?php echo serialize($csv); ?>'>
<?php endif; ?>

<div class="cfix">
    <div style="width: 40%;float: left;">
        <h3><?php echo Yii::t('app','Information') ?></h3>
        <div class="control-group" style="margin-bottom: 0;">
            <div class="controls" style="margin-left: 10px;">
                <label class="radio" for="TestForm_radioButton">
                    <input name="MassChange[type]" value="setPass" type="radio" checked="checked">
                    <?php echo Yii::t('app','Set passwords Service Guide'); ?>
                </label>
            </div>
        </div>
        <div class="control-group " style="margin-bottom: 0;">
            <div class="controls" style="margin-left: 10px;">
                <label class="radio" for="TestForm_radioButton">
                    <input name="MassChange[type]" value="tariffPlan" type="radio">
                    <?php echo Yii::t('app','Tariff plan change'); ?>
                </label>
            </div>
        </div>
        <div class="control-group " style="margin-bottom: 0;">
            <div class="controls" style="margin-left: 10px;">
                <label class="radio" for="TestForm_radioButton">
                    <input name="MassChange[type]" value="setPA" type="radio">
                    <?php echo Yii::t('app','Set PA'); ?>
                </label>
            </div>
        </div>
        <div class="control-group " style="margin-bottom: 0;">
            <div class="controls" style="margin-left: 10px;">
                <label class="radio" for="TestForm_radioButton">
                    <input name="MassChange[type]" value="numberPrice" type="radio">
                    <?php echo Yii::t('app','Number price'); ?>
                </label>
            </div>
        </div>
        <div class="control-group " style="margin-bottom: 0;">
            <div class="controls" style="margin-left: 10px;">
                <label class="radio" for="TestForm_radioButton">
                    <input name="MassChange[type]" value="shortNumber" type="radio">
                    <?php echo Yii::t('app','Short number'); ?>
                </label>
            </div>
        </div>
        <div class="control-group " style="margin-bottom: 0;">
            <div class="controls" style="margin-left: 10px;">
                <label class="radio" for="TestForm_radioButton">
                    <input name="MassChange[type]" value="replaceICC" type="radio">
                    <?php echo Yii::t('app','Replacement ICC'); ?>
                </label>
            </div>
        </div>
        <div class="control-group " style="margin-bottom: 0;">
            <div class="controls" style="margin-left: 10px;">
                <label class="radio" for="TestForm_radioButton">
                    <input name="MassChange[type]" value="balanceStatus" type="radio">
                    <?php echo Yii::t('app','Balance Status'); ?>
                </label>
            </div>
        </div>
    </div>
    <!--<div style="width: 40%;float: left;">
        <h3><?php echo Yii::t('app','Changes') ?></h3>
        <div class="control-group " style="margin-bottom: 0;">
            <div class="controls" style="margin-left: 10px;">
                <label class="radio" for="TestForm_radioButton">
                    <input name="MassChange[type]" value="replaceICC" type="radio">
                    <?php echo Yii::t('app','Replacement ICC'); ?>
                </label>
            </div>
        </div>
        <div class="control-group " style="margin-bottom: 0;">
            <div class="controls" style="margin-left: 10px;">
                <label class="radio" for="TestForm_radioButton">
                    <input name="MassChange[type]" value="replaceICCWith" type="radio">
                    <?php echo Yii::t('app','Replacement ICC with exemption'); ?>
                </label>
            </div>
        </div>
    </div>-->
    <div style="width: 20%;float: left;">
        <h3><?php echo Yii::t('app','Status') ?></h3>
        <button name="massFree" value="1" class="btn" type="submit" onclick="return confirm('Вы уверены?');">Освободить номер</button>
        <button name="massFreeWait" value="1" class="btn" type="submit" onclick="return confirm('Вы уверены?');" style="margin-top: 10px;">Отложенная сим</button>
        <button name="massStatusUnknown" value="1" class="btn" type="submit" onclick="return confirm('Вы уверены?');" style="margin-top: 10px;">Неизвестный</button>
        <button name="massRecovery" value="1" class="btn" type="submit" onclick="return confirm('Вы уверены?');" style="margin-top: 10px;">На восстановление</button>
    </div>
</div>


<div class="form-actions" style="padding: 20px;">
    <button name="action" value="restoreToBase" class="btn" type="submit" style="float: left;"><?php echo Yii::t('app','Change'); ?></button>
    <?php /*
    <div class="control-group" style="float: left; margin: 0;">
        <div class="controls" style="margin: 0 0 0 40px;">
            <label class="checkbox">
                <input id="MassChange_operator" value="0" type="checkbox" name="MassChange[operator]">
                <label for="MassChange_operator"><?php echo Yii::t('app','Application form into a megaphone'); ?></label>
            </label>
        </div>
    </div>
    */ ?>
</div>


<?php $this->endWidget(); ?>

<script>
    jQuery(document).ready(function(){
        jQuery('input:radio[name="MassChange[type]"]').change(function(){
            if ($(this).val()=='setPA' || $(this).val()=='numberPrice')
                $('#MassChange_operator').attr('disabled','disabled');
            else $('#MassChange_operator').removeAttr('disabled');
        })
    })
</script>