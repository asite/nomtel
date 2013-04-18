<?php

$this->breadcrumbs = array(
	$model->label(2) => array('admin'),
	$model->adminLabel(Yii::t('app', 'Updating Agent')),
);

?>

<h1><?php echo $model->adminLabel(Yii::t('app', 'Updating Agent')); ?></h1>

<h5>
    Кол-во SIM: <?php echo $model->stat_sim_count; ?>
    <br/>
    Кол-во активных SIM: <?php echo $model->stat_sim_count; ?>
</h5>

<?php
$this->renderPartial('_form', array(
		'model' => $model,
        'user' => $user,
        'referralRates' => $referralRates
));
?>