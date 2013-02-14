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
        'url' => $this->createUrl('sendServiceMail'),
    )); ?>
    <?php $this->widget('bootstrap.widgets.TbButton',array(
        'label' => Yii::t('app','Change the tariff plan'),
        'size' => 'medium',
        'url' => $this->createUrl('sendServiceMail'),
    )); ?>
    <?php $this->widget('bootstrap.widgets.TbButton',array(
        'label' => Yii::t('app','Block'),
        'size' => 'medium',
        'url' => $this->createUrl('sendServiceMail'),
    )); ?>
    <?php $this->widget('bootstrap.widgets.TbButton',array(
        'label' => Yii::t('app','Other question'),
        'size' => 'medium',
        'url' => $this->createUrl('sendServiceMail'),
    )); ?>
</div>
<?php $this->endWidget();?>