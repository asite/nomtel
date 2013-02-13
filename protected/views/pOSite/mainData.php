<div class="number_info">
    <div class="w80 cfix">
        <div style="float:left;width:40%;">
            <?php $this->widget('bootstrap.widgets.TbDetailView',array(
            'data'=>$sim,
            'attributes'=>array(
                'tariff',
            ),
        )); ?>
        </div>
        <div style="float:left;width:20%;">
            <?php $this->widget('bootstrap.widgets.TbDetailView',array(
            'data'=>$sim,
            'attributes'=>array(
                'operator',
            ),
        )); ?>
        </div>
        <div style="float:left;width:40%;">
            <?php $this->widget('bootstrap.widgets.TbDetailView',array(
                'data'=>$sim,
                'attributes'=>array(
                    'operatorRegion',
                ),
        )); ?>
        </div>
    </div>
