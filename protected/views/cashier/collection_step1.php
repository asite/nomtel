<h1>Инкассация: шаг 1 из 2</h1>
<h3>Кассир: <?=$cashier?></h3>
<h3>Баланс кассы: <?=$balance?></h3>

    <?php $form = $this->beginWidget('BaseTbActiveForm', array(
        'id' => 'operator-form',
        'type' => 'horizontal',
        'enableAjaxValidation' => false,
        'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false),
        //'htmlOptions'=>array('enctype'=>'multipart/form-data')
    ));
    ?>

    <p class="note">
        <?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
    </p>

    <?php echo $form->errorSummary($collection); ?>

    <?php echo $form->dropDownListRow($collection,'collector_support_operator_id',SupportOperator::getComboList(array(''=>'')),array('class'=>'span2')); ?>
    <?php echo $form->textFieldRow($collection,'sum',array('class'=>'span2','maxlength'=>200)); ?>

    <?php
    echo '<div class="form-actions">';
    echo CHtml::htmlButton('Далее', array('class'=>'btn', 'type'=>'submit'));
    echo '</div>';
    ?>

    <?php $this->endWidget(); ?>
