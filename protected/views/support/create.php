<?php

$this->breadcrumbs = array(
	$model->label(2) => array('admin'),
	Yii::t('app', 'Creating SupportOperator'),
);

?>

<h1><?php echo Yii::t('app', 'Creating SupportOperator');?></h1>

<?php
$this->renderPartial('_form', array(
		'model' => $model,
        'user' => $user,
		'buttons' => 'create'));
?>