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
	Yii::t('app', 'Creating ".$this->modelClass."'),
);\n";
?>

?>

<h1><?php echo '<?php'; ?> echo Yii::t('app', 'Creating <?php echo $this->modelClass ?>');<?php echo '?>';?></h1>

<?php echo "<?php\n"; ?>
$this->renderPartial('_form', array(
		'model' => $model,
		'buttons' => 'create'));
<?php echo '?>'; ?>