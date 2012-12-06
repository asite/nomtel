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

    $opList = Operator::model()->findAll();
    $opListArray = array();
    foreach($opList as $v) {
      $opListArray[$v['id']]=$v['title'];
    }

    if (isset($_POST['AddSim']['operator'])) $operator_id = $_POST['AddSim']['operator']; else $operator_id = key($opListArray);
    $tariffList = Tariff::model()->findAllByAttributes(array('operator_id'=>$operator_id));
    $tariffListArray = array();
    foreach($tariffList as $v) {
      $tariffListArray[$v['id']]=$v['title'];
    }

    $whereList = Agent::model()->findAll();
    $whereListArray = array(0=>'БАЗА');
    foreach($whereList as $v) {
      $whereListArray[$v['id']]=$v['surname'].' '.$v['name'].' '.$v['middle_name'];
    }

    if ($_POST['AddSim']) {
      $activeTabs = array('tab1'=>false,'tab2'=>false);

      $model->attributes = $_POST['AddSim'];
      $this->performAjaxValidation($model, $_POST['simMethod']);


      if ($_POST['simMethod'] == 'add-much-sim') {
        $result = Sim::model()->findAllByAttributes(
          array(),
          $condition = 'icc >= :iccBegin AND icc <= :iccEnd',
          $params = array(
              ':iccBegin' => $_POST['AddSim']['ICCFirst'].$_POST['AddSim']['ICCBegin'],
              ':iccEnd' => $_POST['AddSim']['ICCFirst'].$_POST['AddSim']['ICCEnd'],
          )
        );
        if (isset($_POST['buttonProcessSim'])) {
          $activeTabs['tab1'] = true;
          $this->render('add', array('model'=>$model,'tariffListArray'=>$tariffListArray, 'opListArray'=>$opListArray, 'whereListArray'=>$whereListArray, 'deliveryReportMany'=>$result, 'activeTabs'=>$activeTabs));
          exit;
        } else {
          foreach($result as $v) {
            $model = Sim::model()->findByAttributes(array('icc'=>$v->icc));
            $model->state = 'IN_BASE';
            $model->operator_id = $_POST['AddSim']['operator'];
            $model->tariff_id = $_POST['AddSim']['tariff'];
            $model->save();
          }
          if ($_POST['AddSim']['where']) {
            $key = rand();
            $_SESSION['moveAgent'][$key]=$_POST['AddSim']['where'];
            foreach($result as $v) {
              $_SESSION['moveSims'][$key][$v->id]=$v->id;
            }
            $this->redirect(array('move','key'=>$key));
            exit;
          } else {
            Yii::app()->user->setFlash('successMany', '<strong>Операция прошла успешно</strong> Данные успешно добавлены.');
            $this->refresh();
            exit;
          }
        }
      }

      if ($_POST['simMethod'] == 'add-few-sim') {
        /*$condition = ' icc="'.$_POST['AddSim']['ICCBeginFew'].$_POST['AddSim']['ICCEndFew'].'" ';
        for($i=1; $i<=count($_POST['AddNewSim']['ICCBeginFew']);$i++) {
          if ($_POST['AddNewSim']['ICCBeginFew'][$i] && $_POST['AddNewSim']['ICCEndFew'][$i])
          $condition .= ' OR icc = "'.$_POST['AddNewSim']['ICCBeginFew'][$i].$_POST['AddNewSim']['ICCEndFew'][$i].'"';
        }

        $result = Sim::model()->findAllBySql('select * from sim where '.$condition);
        if (isset($_POST['buttonProcessSim'])) {
          $activeTabs['tab2'] = true;
          $this->render('add', array('model'=>$model, 'data'=>$_POST, 'tariffListArray'=>$tariffListArray, 'opListArray'=>$opListArray, 'whereListArray'=>$whereListArray, 'deliveryReportFew'=>$result, 'activeTabs'=>$activeTabs));
          exit;
        } else {*/
          $model = new Sim;
          $model->state = 'IN_BASE';
          $model->operator_id = $_POST['AddSim']['operator'];
          $model->tariff_id = $_POST['AddSim']['tariff'];
          $model->personal_account = $_POST['AddSim']['ICCPersonalAccount'];
          $model->icc = $_POST['AddSim']['ICCBeginFew'].$_POST['AddSim']['ICCEndFew'];
          $model->number = $_POST['AddSim']['phone'];
          $model->save();

          for($o=1;$o<=count($_POST['AddNewSim']['ICCPersonalAccount']);$o++) {
            $model = new Sim;
            $model->state = 'IN_BASE';
            $model->operator_id = $_POST['AddSim']['operator'];
            $model->tariff_id = $_POST['AddSim']['tariff'];
            $model->personal_account = $_POST['AddNewSim']['ICCPersonalAccount'][$o];
            $model->icc = $_POST['AddNewSim']['ICCBeginFew'][$o].$_POST['AddNewSim']['ICCEndFew'][$o];
            $model->number = $_POST['AddNewSim']['phone'][$o];
            $model->save();
          }
          Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Данные успешно добавлены.');
          Yii::app()->user->setFlash('tab2', true);
          $this->refresh();
          exit;
        //}
      }
    }

    if (Yii::app()->user->hasFlash('tab2')) $activeTabs['tab2'] = true; else $activeTabs['tab1'] = true;
    $this->render('add', array('model'=>$model, 'tariffListArray'=>$tariffListArray, 'opListArray'=>$opListArray, 'whereListArray'=>$whereListArray, 'activeTabs'=>$activeTabs));
  }

  public function actionMove($key) {
    $criteria = new CDbCriteria();
    $criteria->addInCondition('id', $_SESSION['moveSims'][$key]);
    $dataProvider = new CActiveDataProvider('Sim', array('criteria' => $criteria));

    //$sims = Sim::model()->findAllByPk($_SESSION['moveSims'][$key]);
    $agent = Agent::model()->findByPk($_SESSION['moveAgent'][$key]);
    $this->render('move', array('model'=>$model,'dataProvider'=>$dataProvider,'agent'=>$agent));
  }

  public function actionAjaxcombo() {
    $tariffList = Tariff::model()->findAllByAttributes(array('operator_id'=>$_POST['operatorId']));
    $tariffListArray = array();
    $res = '';
    foreach($tariffList as $v) {
      $res .= '<option value="'.$v['id'].'">'.$v['title'].'</option>';
    }
    echo $res;
  }

  public function actionUpdatePrice($id) {
    if (Yii::app()->getRequest()->getIsPostRequest()) {
      try {
        Yii::import('bootstrap.widgets.TbEditableSaver'); //or you can add import 'ext.editable.*' to config
        $model = new TbEditableSaver('Sim');  // 'User' is classname of model to be updated
        $model->update();
        //echo CJSON::encode(array('success' => true, 'count' => '155'));
      } catch (CDbException $e) {
        $this->ajaxError(Yii::t("app", "Can't edit this object because it is used by another object(s)"));
      }
    } else
      throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
  }

  public function actionRemove($id, $key) {
    if (Yii::app()->getRequest()->getIsPostRequest()) {
      try {
        unset($_SESSION['moveSims'][$key][$id]);
        //echo "155";
      } catch (CDbException $e) {
        $this->ajaxError(Yii::t("app", "Can't delete this object because it is used by another object(s)"));
      }
      if (!Yii::app()->getRequest()->getIsAjaxRequest())
        $this->redirect(array('move','key'=>$key));
    } else
      throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
  }
}