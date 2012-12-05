<?php

class SimController extends BaseGxController {

  protected function performAjaxValidation($model, $id='') {
    if(isset($_POST['ajax']) && $_POST['ajax']===$id) {
      echo CActiveForm::validate($model);
      Yii::app()->end();
    }
  }

  public function actionDelivery() {
    if (isset($_FILES['Delivery'])) {
      $sims = array();
      $transaction = Yii::app()->db->beginTransaction();
      $i=1;
      foreach ($_FILES['Delivery']['tmp_name']['fileField'] as $file) {
        if ($file) {
          $f=fopen($file, 'r') or die("Невозможно открыть файл!");
          try {
            while(!feof($f)) {
              $text = fgets($f);
              $text = preg_replace('/\t/', " ", $text);
              $text = preg_replace('/\r\n|\r|\n/u', "", $text);
              $text = preg_replace('/(\s){2,}/', "$1", $text);
              $sim=explode(" ", $text);
              if ($sim[0] && $sim[1] && $sim[2]) {
                $sims[$i]['id'] = $i;
                $sims[$i]['personalAccount'] = $sim[0];
                $sims[$i]['icc'] = $sim[1];
                $sims[$i++]['phoneNumber'] = $sim[2];
                $model = new Sim;
                $model->state = 'NOT_RECEIVED';
                $model->number_price = Yii::app()->params->numberPrice;
                $model->personal_account = $sim[0];
                $model->icc = $sim[1];
                $model->number = $sim[2];
                $model->save();
              }
            }
          } catch(Exception $e) {}
        }
      }
      $transaction->commit();
      Yii::app()->user->setFlash('deliveryReport',serialize($sims));
      $this->refresh();
      exit;
    }
    $this->render('delivery');
  }

  public function actionAdd() {
    $model = new AddSim;

    if ($_POST['AddSim']) {
      $model->attributes = $_POST['AddSim'];
      $this->performAjaxValidation($model, $_POST['simMethod']);
    }

    $opList = Operator::model()->findAll();
    $opListArray = array();
    foreach($opList as $v) {
      $opListArray[$v['id']]=$v['title'];
    }

    $tariffList = Tariff::model()->findAll();
    $tariffListArray = array();
    foreach($tariffList as $v) {
      $tariffListArray[$v['id']]=$v['title'];
    }

    $whereList = Agent::model()->findAll();
    $whereListArray = array(0=>'БАЗА');
    foreach($whereList as $v) {
      $whereListArray[$v['id']]=$v['surname'].' '.$v['name'].' '.$v['middle_name'];
    }

    $this->render('add', array('model'=>$model,'tariffListArray'=>$tariffListArray, 'opListArray'=>$opListArray, 'whereListArray'=>$whereListArray));
  }

}