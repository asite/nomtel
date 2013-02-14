<h2>Личный кабинет номера <?=$number->formattedNumber?></h2>

<?php
if ($needPassport)
    $this->renderPartial('passport',array('person_files'=>$person_files,'person'=>$person));
else
    $this->renderPartial('mainData',array('number'=>$number,'sim'=>$sim));

?>

<?php $box = $this->beginWidget('bootstrap.widgets.TbBox', array(
    'title' => Yii::t('app','Service Buttons'),
    'headerIcon' => 'icon-th-list',
    // when displaying a table, if we include bootstra-widget-table class
    // the table will be 0-padding to the box
    'htmlOptions' => array('class'=>'bootstrap-widget-table')
));?>
<div class="service_buttons">
    <?php $this->widget('bootstrap.widgets.TbButton',array(
        'label' => Yii::t('app','Restore the card'),
        'size' => 'medium',
        'url' => $this->createUrl('sendRestoreCard'),
    )); ?>
    <?php $this->widget('bootstrap.widgets.TbButton',array(
        'label' => Yii::t('app','Change the tariff plan'),
        'size' => 'medium',
        'url' => $this->createUrl('sendChangeTariff'),
    )); ?>
    <?php $this->widget('bootstrap.widgets.TbButton',array(
        'label' => Yii::t('app','Block'),
        'size' => 'medium',
        'url' => $this->createUrl('sendBlock'),
    )); ?>
    <?php $this->widget('bootstrap.widgets.TbButton',array(
        'label' => Yii::t('app','Other question'),
        'size' => 'medium',
        'url' => $this->createUrl('sendOtherQuestion'),
    )); ?>
</div>
<?php $this->endWidget();?>
<div class="get_specification">
    <?php $form = $this->beginWidget('BaseTbActiveForm', array(
                    'id' => 'specification',
                    'type' => 'horizontal',
                    'action'=>$this->createUrl('sendSpecification'),
                    'enableAjaxValidation' => true,
                    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false)
              ));
        ?>
        <?php echo $form->dateRangeRow($model, 'dateRange',
            array(
                'options' => array(
                    'callback'=>'js:function(start, end){console.log(start.toString("MMMM d, yyyy") + " - " + end.toString("MMMM d, yyyy"));}',
                )
        )); ?>
        <?php $this->widget('bootstrap.widgets.TbButton',array('label'=>Yii::t('app','Order details'))); ?>
    <?php $this->endWidget(); ?>
</div>