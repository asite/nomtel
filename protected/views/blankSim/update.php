<?php

$this->breadcrumbs = array(
	$model->label(2) => array('admin'),
	$model->adminLabel(Yii::t('app', 'Updating BlankSim')),
);

?>

<h1><?php echo $model->adminLabel(Yii::t('app', 'Updating BlankSim')); ?></h1>

<?php
$this->renderPartial('_form', array(
		'model' => $model));
?>