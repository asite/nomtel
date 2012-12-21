<?php

class SimController extends BaseGxController {

  protected function performAjaxValidation($model, $id='') {
    if(isset($_POST['ajax']) && $_POST['ajax']===$id) {
      echo CActiveForm::validate($model);
      Yii::app()->end();
    }
  }

  public function actionDelivery() {
    if (isset($_POST['Delivery']['operator']) && $_POST['Delivery']['operator']==0) {Yii::app()->user->setFlash('error', '<strong>Ошибка: </strong> Не выбран оператор!'); $this->refresh(); exit; }
    if (isset($_FILES['Delivery']) && $_FILES['Delivery']) {
      $sims = array();
      //$transaction = Yii::app()->db->beginTransaction();
      $i=1;
      for($f=1;$f<=count($_FILES['Delivery']['tmp_name']['fileField']);$f++) {
        $file = $_FILES['Delivery']['tmp_name']['fileField'][$f];
        $file_name = $_FILES['Delivery']['name']['fileField'][$f];
        if ($file) {
          if ($_POST['Delivery']['operator']==1) {
            $f=fopen($file, 'r') or die("Невозможно открыть файл!");
            while(!feof($f)) {
              $text = fgets($f);
              $text = preg_replace('/\t/', " ", $text);
              $text = preg_replace('/\r\n|\r|\n/u', "", $text);
              $text = preg_replace('/(\s){2,}/', "$1", $text);
              $sim=explode(" ", $text);
              if (!isset($sim[2])) $sim[2]='';
              if ($sim[0] && $sim[1]) {
                $model = new Sim;
                $model->state = 'NOT_RECEIVED';
                $model->number_price = 0;
                $model->personal_account = $sim[0];
                $model->icc = $sim[1];
                $model->number = $sim[2];
                try {
                  $model->save();
                  $sims[$i]['personal_account'] = $sim[0];
                  $sims[$i]['icc'] = $sim[1];
                  $sims[$i++]['number'] = $sim[2];
                 } catch(Exception $e) {}
              }
            }
          } elseif ($_POST['Delivery']['operator']==2) {
            Yii::import('application.vendors.PHPExcel',true);
            if (preg_match('%\.xls$%',$file_name)) {
              $objReader = new PHPExcel_Reader_Excel5;
              $file_type='xls';
            }
            elseif (preg_match('%\.xlsx$%',$file_name)) {
              $objReader = new PHPExcel_Reader_Excel2007;
              $file_type='xlsx';
            }
            else die('error');
            $objPHPExcel = $objReader->load(@$file);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow();
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

            $sim = array();
            for ($row = 2; $row <= $highestRow; ++$row) {
              for ($col = 0; $col <= $highestColumnIndex; ++$col) {
                $info = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                if ($col==0 && $info!='') $sim[0] = $info;
                if (preg_match('%- (\d{10})$%',$info,$matches)) $sim[1] = $matches[1];
              }
              if ($sim[0] && $sim[1]) {
                $model = new Sim;
                $model->state = 'IN_BASE';
                $model->number_price = 0;
                $model->personal_account = $sim[0];
                $model->number = $sim[1];
                try {
                  $model->save();
                  $sims[$i]['personal_account'] = $sim[0];
                  $sims[$i++]['number'] = $sim[1];
                } catch(Exception $e) { }
              }
            }
          }
        }
      }
      //$transaction->commit();
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

    $whereListArray = array(0=>'БАЗА',1=>'АГЕНТ');


    if ($_POST['AddSim']) {
      $activeTabs = array('tab1'=>false,'tab2'=>false);

      $model->attributes = $_POST['AddSim'];
      $this->performAjaxValidation($model, $_POST['simMethod']);

      if ($_POST['simMethod'] == 'add-much-sim') {
        $result = Sim::model()->findAllByAttributes(
          array(),
          $condition = 'icc >= :iccBegin AND icc <= :iccEnd AND state = :state',
          $params = array(
              ':iccBegin' => $_POST['AddSim']['ICCFirst'].$_POST['AddSim']['ICCBegin'],
              ':iccEnd' => $_POST['AddSim']['ICCFirst'].$_POST['AddSim']['ICCEnd'],
              ':state' => 'NOT_RECEIVED',
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

          if (empty($result)) {
            Yii::app()->user->setFlash('error', '<strong>Ошибка: </strong> Отсутствуют данные для добавления!');
            $activeTabs['tab1'] = true;
            $this->render('add', array('model'=>$model,'tariffListArray'=>$tariffListArray, 'opListArray'=>$opListArray, 'whereListArray'=>$whereListArray, 'deliveryReportMany'=>$result, 'activeTabs'=>$activeTabs));
            exit;
          }

          if ($_POST['AddSim']['where']) {
            $key = rand();
            foreach($result as $v) {
              $_SESSION['moveSims'][$key][$v->id]=$v->id;
            }
            $this->redirect(array('move','key'=>$key));
            exit;
          } else {
            Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Данные успешно добавлены.');
            $this->refresh();
            exit;
          }
        }
      }

      if ($_POST['simMethod'] == 'add-few-sim') {
          $old_model = $model;

          $model = new Sim;
          $model->state = 'IN_BASE';
          $model->number_price = 0;
          $model->operator_id = $_POST['AddSim']['operator'];
          $model->tariff_id = $_POST['AddSim']['tariff'];
          $model->personal_account = $_POST['AddSim']['ICCPersonalAccount'];
          $model->icc = $_POST['AddSim']['ICCBeginFew'].$_POST['AddSim']['ICCEndFew'];
          $model->number = $_POST['AddSim']['phone'];
          try {
            $model->save();
            $ids = array();
            $ids[$model->id] = $model->id;
          } catch(Exception $e) {}

          for($o=1;$o<=count($_POST['AddNewSim']['ICCPersonalAccount']);$o++) {
            $model = new Sim;
            $model->state = 'IN_BASE';
            $model->number_price = 0;
            $model->operator_id = $_POST['AddSim']['operator'];
            $model->tariff_id = $_POST['AddSim']['tariff'];
            $model->personal_account = $_POST['AddNewSim']['ICCPersonalAccount'][$o];
            $model->icc = $_POST['AddNewSim']['ICCBeginFew'][$o].$_POST['AddNewSim']['ICCEndFew'][$o];
            $model->number = $_POST['AddNewSim']['phone'][$o];

            try {
              $model->save();
              $ids[$model->id] = $model->id;
            } catch(Exception $e) {}

          }

          if (empty($ids)) {
            Yii::app()->user->setFlash('error', '<strong>Ошибка: </strong> Отсутствуют данные для добавления(возможно данные уже есть в базе)!');
            $activeTabs['tab2'] = true;
            $this->render('add', array('model'=>$old_model,'tariffListArray'=>$tariffListArray, 'opListArray'=>$opListArray, 'whereListArray'=>$whereListArray, 'deliveryReportMany'=>$result, 'activeTabs'=>$activeTabs));
            exit;
          }

          if ($_POST['AddSim']['where']) {
            $key = rand();
            $_SESSION['moveSims'][$key]=$ids;
            $this->redirect(array('move','key'=>$key));
            exit;
          } else {
            Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Данные успешно добавлены.');
            Yii::app()->user->setFlash('tab2', true);
            $this->refresh();
            exit;
          }
      }
    }

    if (Yii::app()->user->hasFlash('tab2')) $activeTabs['tab2'] = true; else $activeTabs['tab1'] = true;
    $this->render('add', array('model'=>$model, 'tariffListArray'=>$tariffListArray, 'opListArray'=>$opListArray, 'whereListArray'=>$whereListArray, 'activeTabs'=>$activeTabs));
  }

  public function actionMove($key) {
    if ($_POST['Move']) {
      if (!$_POST['Move']['agent_id']) {
        Yii::app()->user->setFlash('error', '<strong>Ошибка</strong> Не выбран агент.');
        $this->refresh();
        exit;
      }
      $totalNumberPrice = Sim::model()->getTotalNumberPrice($_SESSION['moveSims'][$key]);
      $totalSimPrice = count($_SESSION['moveSims'][$key])*$_POST['Move']['PriceForSim'];
      if (count($_SESSION['moveSims'][$key])==0) {
        Yii::app()->user->setFlash('error', '<strong>Ошибка</strong> Отсутствуют данные для передачи.');
        $this->redirect(Yii::app()->createUrl('sim/add'));
        exit;
      }
      $model = new DeliveryReport;
      $model->parent_agent_id = Yii::app()->user->getState('agentId');
      $model->agent_id = $_POST['Move']['agent_id'];
      $model->dt = date('Y-m-d H:i:s', $_POST['Move']['date']);
      $model->sim_price = $_POST['Move']['PriceForSim'];
      $model->sum = $totalNumberPrice + $totalSimPrice;
      $model->save();

      $criteria = new CDbCriteria();
      $criteria->addInCondition('id', $_SESSION['moveSims'][$key]);
      $ids_string = implode(",", $_SESSION['moveSims'][$key]);

      Sim::model()->updateAll(array('agent_id'=>$_POST['Move']['agent_id'], 'delivery_report_id'=>$model->id, 'state'=>'DELIVERED_TO_AGENT'),$criteria);

      $sql = "INSERT INTO sim (state, personal_account, number,number_price, icc, parent_id, parent_agent_id, parent_delivery_report_id, agent_id, delivery_report_id, operator_id, tariff_id)
              SELECT s.state, s.personal_account, s.number,s.number_price, s.icc, s.id ,s.agent_id, ".Yii::app()->db->quoteValue($model->id).", NULL, NULL, s.operator_id, s.tariff_id
              FROM sim as s
              WHERE id IN ($ids_string)";

      Yii::app()->db->createCommand($sql)->execute();

      $model->agent->recalcBalance();
      $model->agent->save();
      Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Данные успешно передены агенту.');
      unset($_SESSION['moveSims'][$key]);
      $this->redirect(Yii::app()->createUrl('site/index'));
    } else {
      $criteria = new CDbCriteria();
      $criteria->addInCondition('id', $_SESSION['moveSims'][$key]);
      $dataProvider = new CActiveDataProvider('Sim', array('criteria' => $criteria));

      $total = Sim::model()->getTotalNumberPrice($_SESSION['moveSims'][$key]);
      $agent = Agent::model()->getComboList();
      $agent = array(0=>Yii::t('app','Select Agent'))+$agent;
      $this->render('move', array('model'=>$model,'dataProvider'=>$dataProvider,'agent'=>$agent, 'totalNumberPrice'=>$total));
    }
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

  public function actionUpdatePrice($id, $key) {
    if (Yii::app()->getRequest()->getIsPostRequest()) {
      try {
        Yii::import('bootstrap.widgets.TbEditableSaver'); //or you can add import 'ext.editable.*' to config
        $model = new TbEditableSaver('Sim');  // 'User' is classname of model to be updated
        $model->update();
        $price = Sim::model()->getTotalNumberPrice($_SESSION['moveSims'][$key]);
        echo CJSON::encode(array('price'=>$price));
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
        $price = Sim::model()->getTotalNumberPrice($_SESSION['moveSims'][$key]);
        echo CJSON::encode(array('count' => count($_SESSION['moveSims'][$key]), 'price'=>$price));
      } catch (CDbException $e) {
        $this->ajaxError(Yii::t("app", "Can't delete this object because it is used by another object(s)"));
      }
      if (!Yii::app()->getRequest()->getIsAjaxRequest())
        $this->redirect(array('move','key'=>$key));
    } else
      throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
  }

  public function actionFindAgent($term) {
    if (Yii::app()->request->isAjaxRequest && $term) {
      $agent = Agent::model()->getComboList();
      $mass = array();
      foreach($agent as $k=>$v) {
        if (strpos($v, $term)!==false) $mass[]=array('label'=>$v,'id'=>$k);
      }
      echo CJSON::encode($mass);
    }
    Yii::app()->end();
  }

    public function actionList()
    {
        if (isset($_REQUEST['passSIM'])) {
            $key=rand();
            $_SESSION['moveSims'][$key]=explode(',',$_POST['ids']);
            $this->redirect(array('sim/move','key'=>$key));
        }

        $model = new Sim('search');
        $model->unsetAttributes();

        if (isset($_GET['Sim']))
            $model->setAttributes($_GET['Sim']);

        $dataProvider=$model->search();
        $dataProvider->criteria->addCondition("state!='NOT_RECEIVED'");

        if (!Yii::app()->user->getState('isAdmin'))
            $dataProvider->criteria->addColumnCondition(array('parent_agent_id'=>Yii::app()->user->getState('agentId')));
        else
            $dataProvider->criteria->addCondition('parent_agent_id is null');

        $this->render('list', array(
            'model' => $model,
            'dataProvider' => $dataProvider
        ));
    }
}