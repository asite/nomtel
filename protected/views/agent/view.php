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
    <?php $this->widget('bootstrap.widgets.TbDetailView',array(
    'data'=>$model,
    'attributes'=>array(
        'balance',
    ),
)); ?>
</div>
<div style="float:left;width:30%;">
    <?php $this->widget('bootstrap.widgets.TbDetailView',array(
    'data'=>$model,
    'attributes'=>array(
        'paymentsSum',
    ),
)); ?>
</div>
<div style="float:left;width:40%;">
    <?php $this->widget('bootstrap.widgets.TbDetailView',array(
    'data'=>$model,
    'attributes'=>array(
        'deliveryReportsSum',
    ),
)); ?>
</div>
<div class="cfix"></div>


<?php         if (Yii::app()->user->getState('isAdmin')) {   ?>

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

        <?php
        echo '<div class="form-actions">';
        echo CHtml::htmlButton('<i class="icon-ok icon-white"></i> '.Yii::t('app', 'Create Payment'), array('class'=>'btn btn-primary', 'type'=>'submit'));
        echo '</div>';
        $this->endWidget();
        ?>
    </div>    
<?php } ?>

<h2><?php echo Yii::t('app','Account history'); ?></h2>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
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
            'name'=>'sum',
            'htmlOptions' => array('style'=>'text-align:center;'),
            'header'=>Yii::t('app','Sum'),
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'htmlOptions' => array('style'=>'width:80px;text-align:center;vertical-align:middle'),
            'template'=>'{view}',
            'buttons'=>array(
                'view'=>array(
                    'visible'=>'$data["type"]==1',
                    'url'=>'Yii::app()->controller->createUrl("deliveryReport/view",array("id"=>$data["id"]))'
                )
            )
        ),
    ),
)); ?>
