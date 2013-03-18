<h2><?php echo Yii::t('app', 'Your Tariff'); ?></h2>
<div class="number_info">
        <?php $this->widget('bootstrap.widgets.TbEditableDetailView',array(
            'htmlOptions' => array('class'=> 'table table-condensed margin_b0'),
            'data'=>$sim,
            'attributes'=>array(
                'tariff',
            ),
        )); ?>
        <div class="clear"></div>
        <?=$sim->tariff->description;?>
</div>