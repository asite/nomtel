<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */

// this function taken from BootstrapCode class
function generateActiveRow($modelClass, $column)
{
    if ($column->type === 'boolean')
        return "\$form->checkBoxRow(\$model,'{$column->name}')";
    else if (stripos($column->dbType,'text') !== false)
        return "\$form->textAreaRow(\$model,'{$column->name}',array('rows'=>6, 'cols'=>50, 'class'=>'span8'))";
    else if (stripos($column->dbType,'timestamp') !== false || stripos($column->dbType,'datetime') !== false)
        return "\$form->pickerDateTimeRow(\$model,'{$column->name}',array('class'=>'span2'))";
    else if (stripos($column->dbType,'date') !== false)
        return "\$form->pickerDateRow(\$model,'{$column->name}',array('class'=>'span2'))";
    else
    {
        if (preg_match('/^(password|pass|passwd|passcode)$/i',$column->name))
            $inputField='passwordFieldRow';
        else
            $inputField='textFieldRow';

        if ($column->type!=='string' || $column->size===null)
            return "\$form->{$inputField}(\$model,'{$column->name}',array('class'=>'span5'))";
        else
            return "\$form->{$inputField}(\$model,'{$column->name}',array('class'=>'span5','maxlength'=>$column->size))";
    }
}

?>
<div class="form">

<?php $ajax = ($this->enable_ajax_validation) ? 'true' : 'false'; ?>

<?php echo '<?php '; ?>
$form = $this->beginWidget('BaseTbActiveForm', array(
	'id' => '<?php echo $this->class2id($this->modelClass); ?>-form',
    'type' => 'horizontal',
	'enableAjaxValidation' => <?php echo $ajax; ?>,
    <?php if ($this->enable_ajax_validation) { ?>
    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false),
    <?php } ?>
	//'htmlOptions'=>array('enctype'=>'multipart/form-data')
));
<?php echo '?>'; ?>


	<p class="note">
		<?php echo "<?php echo Yii::t('app', 'Fields with'); ?> <span class=\"required\">*</span> <?php echo Yii::t('app', 'are required'); ?>"; ?>.
	</p>

	<?php echo "<?php echo \$form->errorSummary(\$model); ?>\n"; ?>

    <?php
    foreach($this->tableSchema->columns as $column)
    {
        if($column->autoIncrement)
            continue;
        ?>
        <?php echo "<?php echo ".generateActiveRow($this->modelClass,$column)."; ?>\n"; ?>

        <?php
    }
    ?>

<?php /*
<?php foreach ($this->getRelations($this->modelClass) as $relation): ?>
<?php if ($relation[1] == GxActiveRecord::HAS_MANY || $relation[1] == GxActiveRecord::MANY_MANY): ?>
        <div class="control-group">
            <label class="control-label"><?php echo '<?php'; ?> echo GxHtml::encode($model->getRelationLabel('<?php echo $relation[0]; ?>')); ?></label>
            <div class="controls">
                <?php echo '<?php ' . $this->generateActiveRelationField($this->modelClass, $relation) . "; ?>\n"; ?>
            </div>
        </div>
<?php endif; ?>
<?php endforeach; ?>
*/ ?>

<?php echo "<?php
echo '<div class=\"form-actions\">';
echo CHtml::htmlButton('<i class=\"icon-ok icon-white\"></i> '.Yii::t('app', 'Save'), array('class'=>'btn btn-primary', 'type'=>'submit'));
echo '&nbsp;&nbsp;&nbsp;'.CHtml::htmlButton('<i class=\"icon-remove\"></i> '.Yii::t('app', 'Cancel'), array('class'=>'btn', 'type'=>'button', 'onclick'=>'window.location.href=\''.\$this->createUrl('admin').'\''));
echo '</div>';
\$this->endWidget();
?>\n"; ?>
</div>