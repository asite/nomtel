<?php
/**
 * This is the template for generating the model class of a specified table.
 * - $this: the ModelCode object
 * - $tableName: the table name for this class (prefix is already removed if necessary)
 * - $modelClass: the model class name
 * - $columns: list of table columns (name=>CDbColumnSchema)
 * - $labels: list of attribute labels (name=>label)
 * - $rules: list of validation rules
 * - $relations: list of relations (name=>relation declaration)
 * - $representingColumn: the name of the representing column for the table (string) or
 *   the names of the representing columns (array)
 */
?>
<?php echo "<?php\n"; ?>

/**
 * This is the model base class for the table "<?php echo $tableName; ?>".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "<?php echo $modelClass; ?>".
 *
 * Columns in table "<?php echo $tableName; ?>" available as properties of the model,
<?php if(!empty($relations)): ?>
 * followed by relations of table "<?php echo $tableName; ?>" available as properties of the model.
<?php else: ?>
 * and there are no model relations.
<?php endif; ?>
 *
<?php foreach($columns as $column): ?>
 * @property <?php echo $column->type.' $'.$column->name."\n"; ?>
<?php endforeach; ?>
 *
<?php foreach(array_keys($relations) as $name): ?>
 * @property <?php
	$relationData = $this->getRelationData($modelClass, $name);
	$relationType = $relationData[0];
	$relationModel = $relationData[1];

	switch($relationType) {
		case GxActiveRecord::BELONGS_TO:
		case GxActiveRecord::HAS_ONE:
			echo $relationModel;
			break;
		case GxActiveRecord::HAS_MANY:
		case GxActiveRecord::MANY_MANY:
			echo $relationModel . '[]';
			break;
		default:
			echo 'mixed';
	}
	echo ' $' . $name . "\n";
	?>
<?php endforeach; ?>
 */
abstract class <?php echo $this->baseModelClass; ?> extends <?php echo 'BaseGxActiveRecord'/*$this->baseClass;*/ ?> {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '<?php echo $tableName; ?>';
	}

	public static function label($n = 1) {
		return Yii::t('app', '<?php echo $modelClass; ?>|<?php echo $this->pluralize($modelClass); ?>', $n);
	}

	public static function representingColumn() {
<?php if (is_array($representingColumn)): ?>
		return array(
<?php foreach($representingColumn as $representingColumn_item): ?>
			'<?php echo $representingColumn_item; ?>',
<?php endforeach; ?>
		);
<?php else: ?>
		return '<?php echo $representingColumn; ?>';
<?php endif; ?>
	}

	public function rules() {
		return array(
<?php foreach($rules as $rule): ?>
			<?php echo $rule.",\n"; ?>
<?php endforeach; ?>
<?php foreach ($columns as $name=>$column) if ($column->dbType=='date') {?>
            array('<?php echo $name;?>','date','format'=>'dd.MM.yyyy'),
<?php } ?>
<?php foreach ($columns as $name=>$column) if ($column->dbType=='datetime' || $column->dbType=='timestamp') {?>
            array('<?php echo $name;?>','date','format'=>'dd.MM.yyyy HH:mm:ss'),
<?php } ?>
			array('<?php echo implode(', ', array_keys($columns)); ?>', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
<?php foreach($relations as $name=>$relation): ?>
			<?php echo "'{$name}' => {$relation},\n"; ?>
<?php endforeach; ?>
		);
	}

	public function pivotModels() {
		return array(
<?php foreach($pivotModels as $relationName=>$pivotModel): ?>
			<?php echo "'{$relationName}' => '{$pivotModel}',\n"; ?>
<?php endforeach; ?>
		);
	}

	public function attributeLabels() {
		return array(
<?php foreach($labels as $name=>$label): ?>
<?php if($label === null): ?>
			<?php echo "'{$name}' => null,\n"; ?>
<?php else: ?>
			<?php echo "'{$name}' => {$label},\n"; ?>
<?php endif; ?>
<?php endforeach; ?>
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

<?php foreach($columns as $name=>$column): ?>
<?php $partial = ($column->type==='string' and !$column->isForeignKey); ?>
		$criteria->compare('<?php echo $name; ?>', $this-><?php echo $name; ?><?php echo $partial ? ', true' : ''; ?>);
<?php endforeach; ?>

		$dataProvider=new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));

        $dataProvider->pagination->pageSize=self::ITEMS_PER_PAGE;
        return $dataProvider;
	}

<?php
    $hasDateTimeFields=false;
    foreach ($columns as $name=>$column) if ($column->dbType=='date' || $column->dbType=='datetime' ||$column->dbType=='timestamp') $hasDateTimeFields=true;
    if ($hasDateTimeFields) { ?>
    public function convertDateTimeFieldsToEDateTime() {
        // rest of work will do setAttribute() routine
<?php foreach ($columns as $name=>$column) if ($column->dbType=='date' || $column->dbType=='datetime' ||$column->dbType=='timestamp') {?>
        $this->setAttribute('<?php echo $name; ?>',strval($this-><?php echo $name; ?>));
<?php } ?>
    }

    public function convertDateTimeFieldsToString() {
<?php foreach ($columns as $name=>$column) if ($column->dbType=='date') {?>
        if (is_object($this-><?php echo $name; ?>) && get_class($this-><?php echo $name; ?>)=='EDateTime') $this-><?php echo $name; ?>=new EString($this-><?php echo $name; ?>->format(self::$mySqlDateFormat));
<?php } ?>
<?php foreach ($columns as $name=>$column) if ($column->dbType=='datetime' || $column->dbType=='timestamp') {?>
        if (is_object($this-><?php echo $name; ?>) && get_class($this-><?php echo $name; ?>)=='EDateTime') $this-><?php echo $name; ?>=new EString($this-><?php echo $name; ?>->format(self::$mySqlDateTimeFormat));
<?php } ?>
    }

    public function afterFind() {
        $this->convertDateTimeFieldsToEDateTime();
    }

    private function convertStringToEDateTime($val,$type) {
        if (!$val) return null;
        try {
            $val=new EDateTime($val,null,$type);
        } catch (Exception $e) {
        }
        return $val;
    }

    public function setAttribute($name,$value) {
        if (is_string($value)) {
<?php foreach ($columns as $name=>$column) if ($column->dbType=='date') {?>
            if ($name=='<?php echo $name; ?>') $value=$this->convertStringToEDateTime($value,'date');
<?php } ?>
<?php foreach ($columns as $name=>$column) if ($column->dbType=='datetime' || $column->dbType=='timestamp') {?>
            if ($name=='<?php echo $name; ?>') $value=$this->convertStringToEDateTime($value,'datetime');
<?php } ?>
        }
        return parent::setAttribute($name,$value);
    }

    public function beforeSave() {
        $this->convertDateTimeFieldsToString();

        return true;
    }

    public function afterSave() {
        $this->convertDateTimeFieldsToEDateTime();
    }
<?php } ?>
}