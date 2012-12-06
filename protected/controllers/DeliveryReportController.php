<?php

class DeliveryReportController extends BaseGxController {


  public function actionList() {
    $model = new DeliveryReport('search');
    $model->unsetAttributes();

    if (isset($_GET['DeliveryReport']))
      $model->setAttributes($_GET['DeliveryReport']);

    $this->render('list', array(
      'model' => $model,
    ));
  }

  public function actionView($id) {
    $model=$this->loadModel($id,'DeliveryReport');

    $sim = new Sim('search');
    $sim->unsetAttributes();

      if (isset($_GET['Sim']))
          $sim->setAttributes($_GET['Sim']);


    $sim->delivery_report_id=$id;

    $this->render('view', array(
        'model'=>$model,
        'sim'=>$sim
    ));
  }

}