<div class="number_info">
    <div class="w80 cfix">
        <div style="float:left;width:40%;">
            <?php $this->widget('bootstrap.widgets.TbDetailView',array(
            'htmlOptions' => array('class'=> 'table margin_b0 table-condensed'),
            'data'=>$sim,
            'attributes'=>array(
                'tariff',
            ),
        )); ?>
        </div>
        <div style="float:left;width:20%;">
            <?php $this->widget('bootstrap.widgets.TbDetailView',array(
            'htmlOptions' => array('class'=> 'table margin_b0 table-condensed'),
            'data'=>$sim,
            'attributes'=>array(
                'operator',
            ),
        )); ?>
        </div>
        <div style="float:left;width:40%;">
            <?php $this->widget('bootstrap.widgets.TbDetailView',array(
                'htmlOptions' => array('class'=> 'table margin_b0 table-condensed'),
                'data'=>$sim,
                'attributes'=>array(
                    'operatorRegion',
                ),
        )); ?>
        </div>
    </div>
    <div class="w80 cfix">
        <?php $this->widget('bootstrap.widgets.TbDetailView',array(
            'htmlOptions' => array('class'=> 'backgroundwhite'),
            'data'=>$agreement,
            'attributes'=>array(
                array(
                    'label'=>Yii::t('app','Abonent'),
                    'value'=>$agreement->person
                    ),
            ),
        )); ?>
    </div>
</div>