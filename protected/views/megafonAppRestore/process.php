<?php

$this->breadcrumbs = array(
    Yii::t('app', 'Обработка заявления'),
);

?>

<h1>Обработка заявления</h1>


<?php $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'restore-process-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => false,
    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false),
));
?>

<p class="note">
    <?=Yii::t('app', 'Fields with')?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
</p>

<style>
    #s2id_MegafonAppRestoreNumberProcess_number {
        margin-left:0;
    }
</style>

<script>
    function autocompleteGetData(term,page) {
        return {
            q:term
        }
    }

    function autocompleteGetResults(data,page) {
        return data;
    }

    function getNumbers() {
        return new Array();
    }
</script>

<?=$form->select2Row($processModel,'number',array('asDropDownList'=>false,'options'=>array(
    'placeholder'=>'Поиск номера',
    'minimumInputLength'=>3,
    'formatNoMatches'=>'js:function(term){return "Совпадений нет";}',
    'formatSearching'=>'js:function(term){return "Поиск...";}',
    'formatInputTooShort'=>'js:function(term){return "Введите минимум 3 цифры для поиска";}',
    'ajax'=>array(
        'url'=>$this->createurl('numberAutocomplete'),
        'dataType'=>'json',
        'data'=>'js:autocompleteGetData',
        'results'=>'js:autocompleteGetResults'
    ),
),'class'=>'span2'))?>

<?=$form->textFieldRow($processModel,'icc',array('class'=>'span2'))?>

<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton',array(
        'buttonType'=>'submit',
        'label'=>'Восстановить'
    ));?>
</div>

<?php $this->endWidget(); ?>


<?php $this->widget('TbExtendedGridViewExport', array(
    'id' => 'sim-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter' => $model,
    'columns' => array(
        array(
            'name'=>'number',
            'header'=>Yii::t('app','Number'),
            'htmlOptions' => array('style'=>'text-align:center;'),
        ),
        array(
            'name'=>'dt',
            'header'=>'Дата заявления',
            'value'=>'new EDateTime($data["dt"],null,"date")',
            'htmlOptions' => array('style'=>'text-align:center;'),
        ),
        array(
            'name'=>'id',
            'header'=>'Номер заявления',
            'htmlOptions' => array('style'=>'text-align:center;'),
        ),
    ),
)); ?>

