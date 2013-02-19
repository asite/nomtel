<h2><?php echo Yii::t('app', 'Support'); ?></h2>
<br/>
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

    <div class="get_specification">
    <?php $form = $this->beginWidget('BaseTbActiveForm', array(
                    'id' => 'specification',
                    'type' => 'horizontal',
                    'action'=>$this->createUrl('sendSpecification'),
                    'enableAjaxValidation' => true,
                    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false)
              ));
        ?>
        <?php echo $form->errorSummary($model); ?>
        <?php echo $form->dateRangeRow($model, 'dateRange',
            array(
                'errorOptions'=>array('hideErrorMessage'=>true),
                'prepend'=>'<i class="icon-calendar"></i>',
                'options' => array(
                    'format' => 'dd.mm.yyyy'
                )
        )); ?>
        <?php echo CHtml::htmlButton(Yii::t('app','Order details'), array('class'=>'btn', 'type'=>'submit')); ?>
    <?php $this->endWidget(); ?>
</div>
</div>
<?php $this->endWidget();?>