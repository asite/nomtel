<h2><?php echo Yii::t('app', 'Your Tariff'); ?></h2>
<div class="number_info">
        <?php $this->widget('bootstrap.widgets.TbDetailView',array(
            'htmlOptions' => array('class'=> 'table margin_b0 table-condensed'),
            'data'=>$sim,
            'attributes'=>array(
                'tariff',
            ),
        )); ?>
</div>