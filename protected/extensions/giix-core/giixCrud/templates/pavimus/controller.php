<?php
/**
 * This is the template for generating a controller class file for CRUD feature.
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php echo "<?php\n"; ?>

class <?php echo $this->controllerClass; ?> extends <?php echo 'BaseGxController'/*$this->baseControllerClass;*/ ?> {

<?php
  $authpath = 'ext.giix-core.giixCrud.templates.default.auth.';
  Yii::app()->controller->renderPartial($authpath . $this->authtype);
?>

  public function actionCreate() {
    $model = new <?php echo $this->modelClass; ?>;

<?php if ($this->enable_ajax_validation): ?>
    $this->performAjaxValidation($model, '<?php echo $this->class2id($this->modelClass)?>-form');
<?php endif; ?>

    if (isset($_POST['<?php echo $this->modelClass; ?>'])) {
      $model->setAttributes($_POST['<?php echo $this->modelClass; ?>']);
<?php if ($this->hasManyManyRelation($this->modelClass)): ?>
      $relatedData = <?php echo $this->generateGetPostRelatedData($this->modelClass, 4); ?>;
<?php endif; ?>

<?php if ($this->hasManyManyRelation($this->modelClass)): ?>
      if ($model->validate()) {
        $model->saveWithRelated($relatedData);
<?php else: ?>
      if ($model->validate()) {
        $model->save();
<?php endif; ?>
        if (Yii::app()->getRequest()->getIsAjaxRequest())
          Yii::app()->end();
        else
          $this->redirect(array('admin'));
      }
    }

    $this->render('create', array( 'model' => $model));
  }

  public function actionUpdate($id) {
    $model = $this->loadModel($id, '<?php echo $this->modelClass; ?>');

<?php if ($this->enable_ajax_validation): ?>
    $this->performAjaxValidation($model, '<?php echo $this->class2id($this->modelClass)?>-form');
<?php endif; ?>

    if (isset($_POST['<?php echo $this->modelClass; ?>'])) {
      $model->setAttributes($_POST['<?php echo $this->modelClass; ?>']);
<?php if ($this->hasManyManyRelation($this->modelClass)): ?>
      $relatedData = <?php echo $this->generateGetPostRelatedData($this->modelClass, 4); ?>;
<?php endif; ?>

<?php if ($this->hasManyManyRelation($this->modelClass)): ?>
      if ($model->validate()) {
        $model->saveWithRelated($relatedData);
<?php else: ?>
      if ($model->validate()) {
        $model->save();
<?php endif; ?>
        $this->redirect(array('admin'));
      }
    }

    $this->render('update', array(
        'model' => $model,
        ));
  }

  public function actionDelete($id) {
    if (Yii::app()->getRequest()->getIsPostRequest()) {
	  try {
        $this->loadModel($id, '<?php echo $this->modelClass; ?>')->delete();
	  } catch (CDbException $e) {
		$this->ajaxError(Yii::t("app","Can't delete this object because it is used by another object(s)"));
	  }

      if (!Yii::app()->getRequest()->getIsAjaxRequest())
        $this->redirect(array('admin'));
    } else
      throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
  }

  public function actionAdmin() {
    $model = new <?php echo $this->modelClass; ?>('search');
    $model->unsetAttributes();

    if (isset($_GET['<?php echo $this->modelClass; ?>']))
      $model->setAttributes($_GET['<?php echo $this->modelClass; ?>']);

    $this->render('admin', array(
      'model' => $model,
    ));
  }

}