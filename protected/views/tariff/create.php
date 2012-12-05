<?php

$this->breadcrumbs = array(
    $model->operator->adminLabel(Yii::t('app', 'Updating Operator'))=>array('operator/update','id'=>$model->operator->id),
	Yii::t('app', 'Creating Tariff'),
);

?>

<h1><?php echo Yii::t('app', 'Creating Tariff');?></h1>

<?php
$this->renderPartial('_form', array(
		'model' => $model,
		'buttons' => 'create'));
?>