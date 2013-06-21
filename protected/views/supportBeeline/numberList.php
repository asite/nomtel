<h1>Список номеров</h1>

<?php $this->widget('TbExtendedGridViewExport', array(
    'id' => 'number-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'filter' => $model,
    'afterAjaxUpdate' => 'js:function(id,data){multiPageSelRestore(id)}',
    'columns' => array(
        array(
            'name'=>'number',
            'header'=>Yii::t('app','Number')
        ),
        array(
            'name'=>'name',
            'header'=>Yii::t('app','Name')
        ),
        array(
            'name'=>'surname',
            'header'=>Yii::t('app','Surname')
        ),
        array(
            'name'=>'middle_name',
            'header'=>Yii::t('app','Middle Name')
        ),
        array(
            'header'=>Yii::t('app','Passport data'),
            'value'=>'$data["passport_series"].$data["passport_number"].". Выдан: ".$data["passport_issue_date"].", ".$data["passport_issuer"]."."',
        ),
        array(
            'name'=>'registration_address',
            'header'=>Yii::t('app','Registration address')
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'htmlOptions' => array('style'=>'width:40px;text-align:center;vertical-align:middle'),
            'template'=>'{view}',
            'buttons'=>array(
                'view'=>array(
                    'url'=>'Yii::app()->createUrl("subscriptionAgreement/update",array("number_id"=>$data["id"]))',
                ),
            )
        ),
    ),
)); ?>
