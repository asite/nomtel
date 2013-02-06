<?php

$this->breadcrumbs = array(
    Yii::t('app','Statistic')
);
?>

<h1><?=Yii::t('app','Statistic')?></h1>
<h3><?php echo Yii::t('app','Operator'); ?>: <?php echo CHtml::encode($supportOperator) ?></h3>
<br/>
<table class="table">
    <thead>
    <tr>
        <th><?php echo Yii::t('app','Action') ?></th>
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