<?php

$this->breadcrumbs = array(
    Yii::t('app','Statistic')
);
?>

<h1><?=Yii::t('app','Statistic')?></h1>
<h2><?php echo Yii::t('app','Operator'); ?>: <?php echo CHtml::encode($supportOperator) ?></h2>
<br/>
<h3>По номерам</h3>
<table class="table">
    <thead>
    <tr>
        <th style="width:300px;"><?php echo Yii::t('app','Action') ?></th>
        <th><?php echo Yii::t('app','Number of completed') ?></th>
    </tr>
    </thead>
    <tbody>
        <?php foreach($data as $k=>$v) { ?>
            <tr>
                <td><?php echo Number::getSupportStatusLabel($k) ?></td>
                <td><?php echo $v ?></td>
            </tr>
        <?php }; ?>
    </tbody>
</table>

<h3>По обращениям</h3>
<table class="table">
    <thead>
    <tr>
        <th style="width:300px;">Тип</th>
        <th>Количество</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($ticketsStats as $k=>$v) { ?>
    <tr>
        <td><?=CHtml::encode($k)?></td>
        <td><?=CHtml::encode($v)?></td>
    </tr>
        <?php }; ?>
    </tbody>
</table>