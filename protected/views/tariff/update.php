<?php

$this->breadcrumbs = array(
    $model->operator->label(2)=>array('operator/admin'),
    $model->operator->adminLabel(Yii::t('app', 'Updating Operator'))=>array('operator/update','id'=>$model->operator->id),
	$model->adminLabel(Yii::t('app', 'Updating Tariff')),
);

?>

<h1><?php echo $model->adminLabel(Yii::t('app', 'Updating Tariff')); ?></h1>

<?php
$this->renderPartial('_form', array(
		'model' => $model));
?>