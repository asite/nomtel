<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php
echo "<?php\n
\$this->breadcrumbs = array(
	\$model->label(2) => array('admin'),
	\$model->adminLabel(Yii::t('app', 'Updating ".$this->modelClass."')),
);\n";
?>

?>

<h1><?php echo '<?php'; ?> echo $model->adminLabel(Yii::t('app', 'Updating <?php echo $this->modelClass ?>')); ?></h1>

<?php echo "<?php\n"; ?>
$this->renderPartial('_form', array(
		'model' => $model));
?>