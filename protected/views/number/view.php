<?php

$this->breadcrumbs = array(
    Number::label(2)=>$this->createUrl('list'),
    $number->adminLabel(Number::label(1))
);

?>

<h1><?=$number->adminLabel(Number::label(1))?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
    'data'=>$number,
    'attributes'=>array(
        'number',
        'sim.tariff'
    ),
)); ?>

