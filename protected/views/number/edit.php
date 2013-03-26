<?php

if (isSupport()) {
    $this->breadcrumbs = array(
        Number::label(2)=>$this->createUrl('support/numberStatus'),
        $number->adminLabel(Number::label(1))
    );
} else {
    $this->breadcrumbs = array(
        Sim::label(2)=>$this->createUrl('sim/list'),
        $number->adminLabel(Number::label(1))
    );
}

?>

<h1><?=$number->adminLabel(Number::label(1))?></h1>

<div>

    <div style="float:right;">
        <?php
            $this->widget('bootstrap.widgets.TbButton',array(
                'url'=>$this->createUrl('number/setServicePassword',array('id'=>$number->id)),
                'label'=>Yii::t('app','get Service Gid'),
                'htmlOptions'=>array('onclick'=>'return confirm("Вы уверены?");'),
            ));
        ?>
        <?php
            $this->widget('bootstrap.widgets.TbButton',array(
                'url'=>$this->createUrl('number/free',array('id'=>$number->id)),
                'label'=>"Освободить номер",
                'htmlOptions'=>array('onclick'=>'return confirm("Вы уверены?");'),
            ));
        ?>
        <?php
            $this->widget('bootstrap.widgets.TbButton',array(
                'url'=>$this->createUrl('number/free',array('id'=>$number->id,'icc'=>true)),
                'label'=>"Отложенная сим",
                'htmlOptions'=>array('onclick'=>'return confirm("Вы уверены?");'),
            ));
        ?>
    </div>

<?php
if ($number->status!=Number::STATUS_FREE) {
    $this->widget('bootstrap.widgets.TbButton',array(
        'url'=>$this->createUrl('subscriptionAgreement/update',array('number_id'=>$number->id)),
        'label'=>SubscriptionAgreement::label()
    ));
}
?>

</div>
<div style="clear:both;"></div>
<br/>

<div class="number_info">
    <div class="w80 cfix">
        <div style="float:left;width:33%;">
        <?php $this->widget('bootstrap.widgets.TbDetailView',array(
            'htmlOptions' => array('class'=> 'table margin_b0 table-striped table-condensed'),
            'data'=>$number,
            'attributes'=>array(
                'number'
            ),
        )); ?>
        <?php $this->widget('bootstrap.widgets.TbEditableDetailView',array(
            'htmlOptions' => array('class'=> 'table margin_b0 table-condensed'),
            'data'=>$sim,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','Tariff')."<br/><br/>",
                    'name' => 'tariff_id',
                    'value' => $sim->tariff,
                    'editable' => array(
                        'type'   => 'select',
                        'url' => $this->createUrl('number/saveTariff',array('id'=>$number->id)),
                        'options' => array(
                            'params' => array('YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
                        ),
                        'source' => CHtml::listData(Tariff::model()->findAllByAttributes(array('operator_id'=>$number->sim->operator_id)), 'id', 'title'),
                        'success'   => 'js: function(data) {
                            $.fn.yiiGridView.update("number-grid");
                        }'

                    )
                )
            ),
        )); ?>
        <?php $this->widget('bootstrap.widgets.TbDetailView',array(
            'htmlOptions' => array('class'=> 'table table-striped table-condensed'),
            'data'=>$number,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','the Agent'),
                    'value'=>$sim->parentAgent
                    )
            ),
        )); ?>
        </div>
        <div style="float:left;width:34%;">
        <?php $this->widget('bootstrap.widgets.TbDetailView',array(
            'htmlOptions' => array('class'=> 'table margin_b0 table-striped table-condensed'),
            'data'=>$number,
            'attributes'=>array(
                'sim.operator'
            ),
        )); ?>
        <?php $this->widget('bootstrap.widgets.TbEditableDetailView',array(
            'htmlOptions' => array('class'=> 'table margin_b0 table-condensed'),
            'data'=>$sim,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','OperatorRegion')."<br/><br/>",
                    'name' => 'operator_region_id',
                    'value' => $sim->operatorRegion,
                    'editable' => array(
                        'type'   => 'select',
                        'url' => $this->createUrl('number/saveOperatorRegion',array('id'=>$number->id)),
                        'options' => array(
                            'params' => array('YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
                        ),
                        'source' => CHtml::listData(OperatorRegion::model()->findAllByAttributes(array('operator_id'=>$number->sim->operator_id)), 'id', 'title'),
                        'success'   => 'js: function(data) {
                            $.fn.yiiGridView.update("number-grid");
                        }'
                    )
                )
            ),
        )); ?>
        <?php $this->widget('bootstrap.widgets.TbEditableDetailView',array(
            'htmlOptions' => array('class'=> 'table table-striped table-condensed'),
            'data'=>$sim,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','ICC'),
                    'name' => 'icc',
                    'value' => $sim->icc,
                    'editable' => array(
                        'type'   => 'text',
                        'url' => $this->createUrl('number/saveICC',array('id'=>$number->id)),
                        'options' => array(
                            'params' => array('YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
                        ),
                        'source' => $sim->icc,
                        'success'   => 'js: function(data) {
                            $.fn.yiiGridView.update("number-grid");
                        }'
                    )
                )
            ),
        )); ?>
        </div>
        <div style="float:left;width:33%;">
        <?php $this->widget('bootstrap.widgets.TbDetailView',array(
            'htmlOptions' => array('class'=> 'table table-striped table-condensed'),
            'data'=>$number,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','Status'),
                    'value'=>Yii::t('app',$number->status),
                    ),
                array(
                    'label'=>Yii::t('app','Balance Status'),
                    'value'=>''
                    ),
                array(
                    'label'=>Yii::t('app','Abonent'),
                    'value'=>$SubscriptionAgreement->person
                    )
            ),
        )); ?>
        </div>
    </div>

    <br/>
    <div class="cfix">
        <div style="float:left;width:35%;">
        <?php $this->widget('bootstrap.widgets.TbEditableDetailView',array(
            'htmlOptions' => array('class'=> 'table margin_b0 table-striped table-condensed'),
            'data'=>$sim,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','Company'),
                    'name' => 'company_id',
                    'value' => $sim->company,
                    'editable' => array(
                        'type'   => 'select',
                        'url' => $this->createUrl('number/saveCompany',array('id'=>$number->id)),
                        'options' => array(
                            'params' => array('YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
                        ),
                        'source' => CHtml::listData(Company::model()->findAll(),'id', 'title'),
                        'success'   => 'js: function(data) {
                            $.fn.yiiGridView.update("number-grid");
                        }'
                    )
                )
            ),
        )); ?>
        <?php $this->widget('bootstrap.widgets.TbEditableDetailView',array(
            'htmlOptions' => array('class'=>'table margin_b0 table-condensed'),
            'data'=>$number,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','Date connection'),
                    'type'=>'html',
                    'value'=>$SubscriptionAgreement->dt
                    )
            ),
        )); ?>
        </div>
        <div style="float:left;width:30%;">
        <?php $this->widget('bootstrap.widgets.TbEditableDetailView',array(
            'htmlOptions' => array('class'=> 'table table-striped table-condensed'),
            'data'=>$number,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','Director'),
                    'value'=>''
                    ),
                array(
                    //'label'=>Yii::t('app','Company'),
                    'name' => 'codeword',
                    'value' => $number->codeword,
                    'editable' => array(
                        'type'   => 'text',
                        'url' => $this->createUrl('number/saveCodeword',array('id'=>$number->id)),
                        'options' => array(
                            'params' => array('YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
                        ),
                        'source' => $number->codeword,
                        'success'   => 'js: function(data) {
                            $.fn.yiiGridView.update("number-grid");
                        }'
                    )
                )
            ),
        )); ?>
        </div>
        <div style="float:left;width:35%;">
        <?php $this->widget('bootstrap.widgets.TbEditableDetailView',array(
            'htmlOptions' => array('class'=> 'table table-striped table-condensed'),
            'data'=>$number,
            'attributes'=>array(
                array(
                    //'label'=>Yii::t('app','Company'),
                    'name' => 'service_password',
                    'value' => $number->service_password,
                    'editable' => array(
                        'type'   => 'text',
                        'url' => $this->createUrl('number/saveServicePassword',array('id'=>$number->id)),
                        'options' => array(
                            'params' => array('YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
                        ),
                        'source' => $number->service_password,
                        'success'   => 'js: function(data) {
                            $.fn.yiiGridView.update("number-grid");
                        }'
                    )
                ),
                array(
                    'name' => 'short_number',
                    'value' => $number->short_number,
                    'editable' => array(
                        'type'   => 'text',
                        'url' => $this->createUrl('number/saveShortNumber',array('id'=>$number->id)),
                        'options' => array(
                            'params' => array('YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
                        ),
                        'source' => $number->short_number,
                        'success'   => 'js: function(data) {
                                        $.fn.yiiGridView.update("number-grid");
                                    }'
                    )
                ),
            ),
        )); ?>
        </div>
    </div>

    <br/>
    <div class="table_margin0 cfix">
        <div style="float:left;width:40%;">
        <?php   $this->widget('bootstrap.widgets.TbDetailView',array(
            'htmlOptions' => array('class'=> 'table table-striped table-condensed'),
            'data'=>$number,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','BAD'),
                    'value'=>''
                ),
                'personal_account',
            ),
        )); ?>
        </div>
        <div style="float:left;width:30%;">
        <?php $this->widget('bootstrap.widgets.TbEditableDetailView',array(
            'htmlOptions' => array('class'=> 'table table-striped table-condensed'),
            'data'=>$number,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','Sim Price'),
                    'name' => 'sim_price',
                    'value'=>$number->sim_price,
                    'editable' => array(
                        'type'   => 'text',
                        'url' => $this->createUrl('number/saveSimPrice',array('id'=>$number->id)),
                        'options' => array(
                            'params' => array('YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
                        ),
                        'source' => $number->sim_price,
                        'success'   => 'js: function(data) {
                            $.fn.yiiGridView.update("number-grid");
                        }'
                    )
                ),
                array(
                    'label'=>Yii::t('app','Balance'),
                    'value'=>Number::getBalanceStatusLabel($number->balance_status)
                )
            ),

        )); ?>
        </div>
        <div style="float:left;width:30%;">
        <?php $this->widget('bootstrap.widgets.TbEditableDetailView',array(
            'htmlOptions' => array('class'=> 'table table-striped table-condensed'),
            'data'=>$number,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','Number Price'),
                    'name' => 'number_price',
                    'value'=>$number->number_price,
                    'editable' => array(
                        'type'   => 'text',
                        'url' => $this->createUrl('number/saveNumberPrice',array('id'=>$number->id)),
                        'options' => array(
                            'params' => array('YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
                        ),
                        'source' => $number->number_price,
                        'success'   => 'js: function(data) {
                            $.fn.yiiGridView.update("number-grid");
                        }'
                    )
                ),
                array(
                    'label'=>'&nbsp;',
                    'value'=>''
                    )
            ),
        )); ?>
        </div>
    </div>
    <div class="cfix">
        <?php

        if ($BonusReportNumber->bonusReport->dt) $turnover = '('.$BonusReportNumber->bonusReport->dt.'): ';
        if ($BonusReportNumber->turnover) $turnover = $BonusReportNumber->turnover.$turnover;

        $this->widget('bootstrap.widgets.TbDetailView',array(
            'htmlOptions' => array('class'=> 'table table-striped table-condensed'),
            'data'=>$number,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','Turnover sim'),
                    'value'=>$turnover
                    )
            ),
        )); ?>
    </div>




        <div class="w300 table_margin0">
           <?php $this->widget('bootstrap.widgets.TbEditableDetailView',array(
                'htmlOptions' => array('class'=> 'table table-striped table-condensed'),
                'data'=>$number,
                'attributes'=>array(
                    array(
                        'name' => 'support_getting_passport_variant',
                        'value' => $number->support_getting_passport_variant,
                        'editable' => array(
                            'type'   => 'text',
                            'url' => $this->createUrl('number/saveSupportGettingPassportVariant',array('id'=>$number->id)),
                            'options' => array(
                                'params' => array('YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
                            ),
                            'source' => $number->support_getting_passport_variant,
                            'success'   => 'js: function(data) {
                                $.fn.yiiGridView.update("number-grid");
                            }'
                        )
                    ),
                ),
            )); ?>
        </div>
       <div class="w300">
            <?php $this->widget('bootstrap.widgets.TbEditableDetailView',array(
                'htmlOptions' => array('class'=> 'table  table-condensed'),
                'data'=>$number,
                'attributes'=>array(
                    array(
                        'name' => 'support_number_region_usage',
                        'value' => $number->support_number_region_usage,
                        'editable' => array(
                            'type'   => 'text',
                            'url' => $this->createUrl('number/saveSupportNumberRegionUsage',array('id'=>$number->id)),
                            'options' => array(
                                'params' => array('YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
                            ),
                            'source' => $number->support_number_region_usage,
                            'success'   => 'js: function(data) {
                                $.fn.yiiGridView.update("number-grid");
                            }'
                        )
                    ),
                    array(
                        'label'=>'&nbsp;',
                        'value'=>''
                    ),
                ),
            )); ?>
        </div>
</div>
<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'number-grid',
    'dataProvider' => $numberHistory->search(),
    'itemsCssClass' => 'table table-striped table-bordered table-condensed',
    'columns' => array(
        'dt',
        array(
            'name'=>'who',
            'type'=>'html',
            'value'=>'NumberHistory::replaceShortcutsByLinks($data->who)',
        ),
        array(
            'name'=>'comment',
            'type'=>'html',
            'value'=>'NumberHistory::replaceShortcutsByLinks($data->comment)',
        ),
    ),
)); ?>
