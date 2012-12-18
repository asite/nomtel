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
      //$transaction = Yii::app()->db->beginTransaction();
      $i=1;

      foreach ($_FILES['Delivery']['tmp_name']['fileField'] as $file) {
        if ($file) {
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
        //}
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
      $model->agent_id = $_POST['Move']['agent_id'];
      $model->dt = date('Y-m-d H:i:s', $_POST['Move']['date']);
      $model->sim_price = $_POST['Move']['PriceForSim'];
      $model->sum = $totalNumberPrice + $totalSimPrice;
      $model->save();

      $criteria = new CDbCriteria();
      $criteria->addInCondition('id', $_SESSION['moveSims'][$key]);
      Sim::model()->updateAll(array('agent_id'=>$_POST['Move']['agent_id'], 'delivery_report_id'=>$model->id, 'state'=>'DELIVERED_TO_AGENT'),$criteria);

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

        $params=array(
            ':parent_id'=>Yii::app()->user->getState('agentId'),
            ':agent_id'=>Yii::app()->user->getState('agentId')
        );
        $sql="select sim.*,agent.id as agent_id,CONCAT_WS(' ',agent.surname,agent.name,agent.middle_name) as agent_name,dr.dt as delivery_report_dt,o.title as operator,t.title as tariff
             from sim ";
        // agents must view only sim, that they received from admin/parent agent
        if (!Yii::app()->user->getState('isAdmin')) {
             $sql.="join sim_delivery_report sdrc on (sdrc.sim_id=sim.id)
             join delivery_report drc on (drc.id=sdrc.delivery_report_id and drc.agent_id=:agent_id)";
        }
        // in agent column show only direct child agents
        $sql.="left outer join sim_delivery_report sdr on (sdr.sim_id=sim.id)
             left outer join delivery_report dr on (dr.id=sdr.delivery_report_id)
             left outer join agent on (agent.id=dr.agent_id and agent.parent_id ".
                (Yii::app()->user->getState('isAdmin') ? 'is null':'=:parent_id').")";
        // attach operator and tariff
        $sql.="left outer join tariff t on (t.id=sim.tariff_id)
               left outer join operator o on (o.id=t.operator_id)";

        $criteria=new CDbCriteria();
        $criteria->addCondition("sim.state!='NOT_RECEIVED'");
        $criteria->addCondition("((dr.id is not null and agent.id is not null) or (dr.id is null and agent.id is null))");
        $sql.=' where '.$criteria->condition;

        $count=Yii::app()->db->createCommand("select count(*) from ($sql) as mytab")->queryScalar($params);
        $dataProvider=new CSqlDataProvider($sql,
            array(
                'params'=>$params,
                'totalItemCount'=>$count,
                'sort'=>array(
                    'defaultOrder'=>'id',
                    'attributes'=>array('delivery_report_dt','agent_name','number','icc','operator','tariff')
                ),
                'pagination'=>array('pageSize'=>BaseGxActiveRecord::ITEMS_PER_PAGE)
            ));

        $simSearch=new SimSearch();

        $this->render('list', array(
            'dataProvider' => $dataProvider,
            'model' => new Sim(),
            'dataModel' => $simSearch
        ));
    }
}