<?php

$this->breadcrumbs = array(
    Agent::model()->label(2)=>array('admin'),
    $model->adminLabel($model->label(1)),
);

?>

<h1><?php echo GxHtml::encode($model->label(1)); ?></h1>

<div style="float:left;width:30%;">
<?php $this->widget('bootstrap.widgets.TbDetailView',array(
    'data'=>$model,
    'attributes'=>array(
        'surname',
        'name',
        'middle_name',
    ),
)); ?>
</div>
<div style="float:left;width:30%;">
    <?php $this->widget('bootstrap.widgets.TbDetailView',array(
    'data'=>$model,
    'attributes'=>array(
        'phone_1',
        'phone_2',
        'phone_3',
    ),
)); ?>
</div>
<div style="float:left;width:40%;">
    <?php $this->widget('bootstrap.widgets.TbDetailView',array(
    'data'=>$model,
    'attributes'=>array(
        'email',
        'skype',
        'icq'
    ),
)); ?>
</div>
<div class="cfix"></div>

<div style="float:left;width:30%;">
    <?php
    $totalSims=$model->getSimCount();
    $activeSims=$model->getActiveSimCount();
    $this->widget('bootstrap.widgets.TbDetailView',array(
    'data'=>$model,
    'attributes'=>array(
            array(
                'value'=>$model->getBalance(),
                'label'=>'Баланс'
            ),
            array(
                'value'=>$totalSims.'('.($totalSims>0 ? number_format(100*$activeSims/$totalSims,1):'0').'%)',
                'label'=>'Кол-во сим'
            ),
    ),
)); ?>
</div>
<div style="float:left;width:30%;">
    <?php $this->widget('bootstrap.widgets.TbDetailView',array(
    'data'=>$model,
    'attributes'=>array(
        array(
            'value'=>$model->getAllPaymentsSum(),
            'label'=>'Сумма начислений'
        ),
    ),
)); ?>
</div>
<div style="float:left;width:40%;">
    <?php $this->widget('bootstrap.widgets.TbDetailView',array(
    'data'=>$model,
    'attributes'=>array(
        array(
            'value'=>$model->getAllActsSum(),
            'label'=>'Сумма списаний'
        ),
    ),
)); ?>
</div>
<div class="cfix"></div>


<?php         if ($this->addPaymentAllowed($model)) {   ?>

    <h2><?php echo Yii::t('app', 'Create Payment');?></h2>


    <div class="form">


        <?php $form = $this->beginWidget('BaseTbActiveForm', array(
        'id' => 'payment-form',
        'type' => 'horizontal',
        'enableAjaxValidation' => true,
        'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false),
        //'htmlOptions'=>array('enctype'=>'multipart/form-data')
    ));
        ?>

        <?php echo $form->textFieldRow($paymentNew,'sum',array('class'=>'span1')); ?>

        <?php echo $form->textFieldRow($paymentNew,'comment',array('class'=>'span4')); ?>

        <?php
        echo '<div class="form-actions">';
        echo CHtml::htmlButton('<i class="icon-ok icon-white"></i> '.Yii::t('app', 'Create Payment'), array('class'=>'btn btn-primary', 'type'=>'submit'));
        echo '</div>';
        $this->endWidget();
        ?>
    </div>


   <h2><?php echo Yii::t('app', 'Debit');?></h2>


    <div class="form">

        <?php $form = $this->beginWidget('BaseTbActiveForm', array(
        'id' => 'debit-form',
        'type' => 'horizontal',
        'enableAjaxValidation' => true,
        'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false),
        //'htmlOptions'=>array('enctype'=>'multipart/form-data')
    ));
        ?>

        <?php echo $form->textFieldRow($paymentAct,'sum',array('class'=>'span1')); ?>

        <?php echo $form->textAreaRow($paymentAct,'comment',array('errorOptions'=>array('hideErrorMessage'=>true),'class'=>'span4','rows'=>5)); ?>

        <?php
        echo '<div class="form-actions">';
        echo CHtml::htmlButton('<i class="icon-ok icon-white"></i> '.Yii::t('app', 'To charge'), array('class'=>'btn btn-primary', 'type'=>'submit'));
        echo '</div>';
        $this->endWidget();
        ?>
    </div>

<?php } ?>
<h2><?php echo Yii::t('app','Account history'); ?></h2>

<h3 class="h3-balance">Баланс: <?php echo $model->balance; ?></h3>

<?php $this->widget('TbExtendedGridViewExport', array(
    'id' => 'log-grid',
    'dataProvider' => $logDataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    //'filter' => $payment,
    'columns' => array(
        array(
            'name'=>'id',
            'htmlOptions' => array('style'=>'text-align:center;'),
            'header'=>Yii::t('app','Payment/Delivery Report Id'),
        ),
        array(
            'name'=>'dt',
            'htmlOptions' => array('style'=>'text-align:center;'),
            'value'=>'new EDateTime($data["dt"])',
            'header'=>Yii::t('app','Dt'),
        ),
        array(
            'name'=>'comment',
            'htmlOptions' => array('style'=>'text-align:center;'),
            'header'=>Yii::t('app','Comment')
        ),
        array(
            'name'=>'sum_inc',
            'htmlOptions' => array('style'=>'text-align:center;'),
            'header'=>'Начисление',
        ),
        array(
            'name'=>'sum_dec',
            'htmlOptions' => array('style'=>'text-align:center;'),
            'header'=>'Списание',
        ),
        array(
            'name'=>'balance',
            'htmlOptions' => array('style'=>'text-align:center;'),
            'header'=>'Баланс',
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'htmlOptions' => array('style'=>'width:80px;text-align:center;vertical-align:middle'),
            'template'=>'{view}',
            'buttons'=>array(
                'view'=>array(
                    'visible'=>'$data["type"]==1',
                    'url'=>'Yii::app()->controller->createUrl("act/view",array("id"=>$data["id"]))'
                )
            )
        ),
    ),
)); ?>
