<?php

class AgentController extends BaseGxController {


  public function actionCreate() {
    $model = new Agent;

    $this->performAjaxValidation($model, 'agent-form');

    if (isset($_POST['Agent'])) {
      $model->setAttributes($_POST['Agent']);

      if ($model->validate()) {
        $model->save();
        if (Yii::app()->getRequest()->getIsAjaxRequest())
          Yii::app()->end();
        else
          $this->redirect(array('admin'));
      }
    }

    $this->render('create', array( 'model' => $model));
  }

  public function actionUpdate($id) {
    $model = $this->loadModel($id, 'Agent');

    $this->performAjaxValidation($model, 'agent-form');

    if (isset($_POST['Agent'])) {
      $model->setAttributes($_POST['Agent']);

      if ($model->validate()) {
        $model->save();
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
        $this->loadModel($id, 'Agent')->delete();
	  } catch (CDbException $e) {
		$this->ajaxError(Yii::t("app","Can't delete this object because it is used by another object(s)"));
	  }

      if (!Yii::app()->getRequest()->getIsAjaxRequest())
        $this->redirect(array('admin'));
    } else
      throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
  }

  public function actionAdmin() {
    $model = new Agent('search');
    $model->unsetAttributes();

    if (isset($_GET['Agent']))
      $model->setAttributes($_GET['Agent']);

    $this->render('admin', array(
      'model' => $model,
    ));
  }

}