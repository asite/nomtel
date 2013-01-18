<?php

class SimController extends BaseGxController {

    public function additionalAccessRules() {
        return array(
            array('allow', 'actions' => array(), 'roles' => array('agent')),
        );
    }

    protected function performAjaxValidation($model, $id = '') {
        if (isset($_POST['ajax']) && $_POST['ajax'] === $id) {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionDelivery() {
        $activeTabs = array('tab1' => false, 'tab2' => false,'tab3' => false);
        $model = new Sim;

        $companyListArray = Company::getDropDownList();
        $regionListArray = OperatorRegion::getDropDownList();
        $tariffListArray = Tariff::getDropDownList();

        if (isset($_POST['Sim'])) {
            $model->setAttributes($_POST['Sim']);
            $this->performAjaxValidation($model, $_POST['simAdd']['method']);
            $data = $_POST['Sim'];
        }


        if (isset($_FILES['Delivery']) && $_FILES['Delivery']) {
            $sims = array();
            $transaction = Yii::app()->db->beginTransaction();
            $i = 0;
            for ($f = 1; $f <= count($_FILES['Delivery']['tmp_name']['fileField']); $f++) {
                $file = $_FILES['Delivery']['tmp_name']['fileField'][$f];
                $file_name = $_FILES['Delivery']['name']['fileField'][$f];
                if ($file) {

                    // add bilain sim
                    if ($_POST['Sim']['operator_id'] == Operator::OPERATOR_BEELINE_ID) {
                        $activeTabs['tab1'] = true;
                        $f = fopen($file, 'r') or die("Невозможно открыть файл!");
                        while (!feof($f)) {
                            $text = fgets($f);
                            $text = preg_replace('/\t/', " ", $text);
                            $text = preg_replace('/\r\n|\r|\n/u', "", $text);
                            $text = preg_replace('/(\s){2,}/', "$1", $text);
                            $sim = explode(" ", $text);


                            if (!isset($sim[2])) $sim[2] = '';

                            if ($sim[0] && $sim[1]) {
                                $model = new Sim;
                                $model->setAttributes($data);
                                $model->number_price = 0;
                                $model->personal_account = $sim[0];
                                $model->icc = $sim[1];
                                $model->number = $sim[2];
                                //print_r($model->countByAttributes(array('icc'=>$sim[1],'number'=>$sim[2]))); exit;
                                //try {
                                if ($model->countByAttributes(array('icc' => $sim[1], 'number' => $sim[2])) == 0) {
                                    $model->save();
                                    $model->parent_id = $model->id;
                                    $model->save();
                                    $sims[$i]['id'] = $model->id;
                                    $sims[$i]['personal_account'] = $sim[0];
                                    $sims[$i]['icc'] = $sim[1];
                                    $sims[$i++]['number'] = $sim[2];
                                }
                                //} catch(Exception $e) {}
                            }
                        }

                    // add megafon sim in base
                    } elseif ($_POST['Sim']['operator_id'] == Operator::OPERATOR_MEGAFON_ID) {
                        $activeTabs['tab2'] = true;
                        Yii::import('application.vendors.PHPExcel', true);
                        if (preg_match('%\.xls$%', $file_name)) {
                            $objReader = new PHPExcel_Reader_Excel5;
                            $file_type = 'xls';
                        } elseif (preg_match('%\.xlsx$%', $file_name)) {
                            $objReader = new PHPExcel_Reader_Excel2007;
                            $file_type = 'xlsx';
                        } else die('error');
                        $objPHPExcel = $objReader->load(@$file);
                        $objWorksheet = $objPHPExcel->getActiveSheet();
                        $highestRow = $objWorksheet->getHighestRow();
                        $highestColumn = $objWorksheet->getHighestColumn();
                        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

                        $sim = array();
                        $sim_count = 0;
                        for ($row = 2; $row <= $highestRow; ++$row) {
                            for ($col = 0; $col <= $highestColumnIndex; ++$col) {
                                $info = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                                if ($col == 0 && $info != '') $sim[0] = $info;
                                if (preg_match('%- (\d{10})$%', $info, $matches)) $sim[1] = $matches[1];
                            }
                            if ($sim[0] && $sim[1]) {
                                $model = new Sim;
                                $model->setAttributes($data);
                                $model->parent_agent_id = adminAgentId();
                                $sim_count++;
                                $model->number_price = 0;
                                $model->personal_account = $sim[0];
                                $model->number = $sim[1];
                                //try {

                                if ($model->countByAttributes(array('number' => $sim[1])) == 0) {
                                    $model->save();
                                    $model->parent_id = $model->id;
                                    $model->save();

                                    Number::addNumber($model);

                                    $sims[$i]['id'] = $model->id;
                                    $sims[$i]['personal_account'] = $sim[0];
                                    $sims[$i++]['number'] = $sim[1];
                                    //} catch(Exception $e) { }
                                }
                            }
                        }
                        Agent::deltaSimCount(adminAgentId(), $sim_count);
                    }
                }
            }
            $transaction->commit();
            Yii::app()->user->setFlash('act', serialize($sims));
            Yii::app()->user->setFlash('activeTabs', serialize($activeTabs));
            $this->refresh();
            exit;
        }
        $activeTabs['tab1'] = true;
        $this->render('delivery', array('model' => $model, 'activeTabs' => $activeTabs, 'company' => $companyListArray, 'regionList' => $regionListArray, 'tariffList' => $tariffListArray));
    }

    private function validate(&$model, $data)
    {

        $result = array();
        $i = 0;
        unset($data['operator']);
        unset($data['tariff']);
        unset($data['where']);

        foreach ($data as $d) {
            $model->unsetAttributes();
            $model->setAttributes($d);
            $model->validate();
            foreach ($model->getErrors() as $attribute => $errors)
                $result[CHtml::activeId($model,'['.$i.']' . $attribute)] = $errors;
            $i++;
        }

        return $result;
    }

    public function actionAdd() {
        $model = new AddSim;
        $addSimByNumbers=new AddSimByNumbers();

        $opListArray = Operator::getComboList();

        if (isset($_POST['AddSim']['operator'])) $operator_id = $_POST['AddSim']['operator']; else $operator_id = key($opListArray);
        $tariffList = Tariff::model()->findAllByAttributes(array('operator_id' => $operator_id));
        $tariffListArray = array();
        foreach ($tariffList as $v) {
            $tariffListArray[$v['id']] = $v['title'];
        }

        $whereListArray = array(0 => 'БАЗА', 1 => 'АГЕНТ');

        if ($_POST['AddSim'] || $_POST['AddSimByNumbers']) {
            $transaction = Yii::app()->db->beginTransaction();

            $activeTabs = array('tab1' => false, 'tab2' => false, 'tab3' => false);

            //add sim in base or in base and to agent
            if ($_POST['simMethod'] == 'add-much-sim') {
                $model->attributes = $_POST['AddSim'];
                $this->performAjaxValidation($model, $_POST['simMethod']);

                $result = Sim::model()->findAllByAttributes(
                    array(),
                    $condition = 'icc >= :iccBegin AND icc <= :iccEnd AND parent_agent_id is null',
                    $params = array(
                        ':iccBegin' => $_POST['AddSim']['ICCFirst'] . $_POST['AddSim']['ICCBegin'],
                        ':iccEnd' => $_POST['AddSim']['ICCFirst'] . $_POST['AddSim']['ICCEnd'],
                    )
                );

                if (isset($_POST['buttonProcessSim'])) {
                    $activeTabs['tab1'] = true;
                    $this->render('add', array('model' => $model, 'tariffListArray' => $tariffListArray, 'opListArray' => $opListArray, 'whereListArray' => $whereListArray, 'actMany' => $result, 'activeTabs' => $activeTabs));
                    exit;
                } else {
                    $sim_count = 0;
                    foreach ($result as $v) {
                        $v->parent_agent_id = adminAgentId();
                        $sim_count++;
                        $v->operator_id = $_POST['AddSim']['operator'];
                        $v->tariff_id = $_POST['AddSim']['tariff'];
                        $v->save();

                        Number::addNumber($v);
                    }
                    Agent::deltaSimCount(adminAgentId(), $sim_count);

                    if (empty($result)) {
                        Yii::app()->user->setFlash('error', '<strong>Ошибка: </strong> Отсутствуют данные для добавления!');
                        $activeTabs['tab1'] = true;
                        $this->render('add', array('model' => $model, 'tariffListArray' => $tariffListArray, 'opListArray' => $opListArray, 'whereListArray' => $whereListArray, 'actMany' => $result, 'activeTabs' => $activeTabs));
                        exit;
                    }

                    if ($_POST['AddSim']['where']) {
                        $key = rand();
                        foreach ($result as $v) {
                            $_SESSION['moveSims'][$key][$v->id] = $v->id;
                        }
                        $transaction->commit();
                        $this->redirect(array('move', 'key' => $key));
                    } else {
                        Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Данные успешно добавлены.');
                        $transaction->commit();
                        $this->refresh();
                        exit;
                    }
                }
            }


            if ($_POST['simMethod'] == 'add-few-sim') {
                if (Yii::app()->getRequest()->getIsAjaxRequest()) {
                    $result = $this->validate($model, $_POST['AddSim']);
                    echo function_exists('json_encode') ? json_encode($result) : CJSON::encode($result);
                    Yii::app()->end();
                }
                $old_model = $model;

                $sim_count = 1;

                for ($o = 0; $o <= count($_POST['AddSim'])-1; $o++) {
                    if (isset($_POST['AddSim'][$o]['phone']) && ($_POST['AddSim'][$o]['phone']))
                    {
                        $model = new Sim;
                        $model->parent_agent_id = adminAgentId();
                        $sim_count++;
                        $model->number_price = 0;
                        $model->operator_id = $_POST['AddSim'][0]['operator'];
                        $model->tariff_id = $_POST['AddSim'][0]['tariff'];
                        $model->personal_account = $_POST['AddSim'][$o]['ICCPersonalAccount'];
                        $model->icc = $_POST['AddSim'][$o]['ICCBeginFew'] . $_POST['AddSim'][$o]['ICCEndFew'];
                        $model->number = $_POST['AddSim'][$o]['phone'];
                        $model->operator_region_id=$_POST['AddSim'][0]['region'];
                        $model->company_id=$_POST['AddSim'][0]['company'];

                        //try {
                        $model->save();
                        $model->parent_id = $model->id;
                        $model->save();

                        Number::addNumber($model);

                        $ids[$model->id] = $model->id;
                        //} catch(Exception $e) {}
                    }
                }

                Agent::deltaSimCount(adminAgentId(), $sim_count);

                if (empty($ids)) {
                    Yii::app()->user->setFlash('error', '<strong>Ошибка: </strong> Отсутствуют данные для добавления(возможно данные уже есть в базе)!');
                    $activeTabs['tab2'] = true;
                    $this->render('add', array('model' => $old_model, 'tariffListArray' => $tariffListArray, 'opListArray' => $opListArray, 'whereListArray' => $whereListArray, 'actMany' => $result, 'activeTabs' => $activeTabs));
                    exit;
                }

                if ($_POST['AddSim'][0]['where']) {
                    $key = rand();
                    $_SESSION['moveSims'][$key] = $ids;
                    $transaction->commit();
                    $this->redirect(array('move', 'key' => $key));
                    exit;
                } else {
                    Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Данные успешно добавлены.');
                    Yii::app()->user->setFlash('tab2', true);
                    $transaction->commit();
                    $this->refresh();
                    exit;
                }
            }

            if ($_POST['simMethod'] == 'add-few-sim2') {
                $this->performAjaxValidation($addSimByNumbers,'add-few-sim2');

                if (isset($_POST['AddSimByNumbers']))
                    $addSimByNumbers->setAttributes($_POST['AddSimByNumbers']);

                if ($addSimByNumbers->validate()) {
                    $sim_count = 1;

                    $numbers=preg_split('%[\r\n]%',$addSimByNumbers->numbers,-1,PREG_SPLIT_NO_EMPTY);

                    foreach($numbers as $number) {
                        $number=trim($number);
                        if (!preg_match('%^\d+$%',$number)) continue;
                        if (Sim::model()->countByAttributes(array('number'=>$number))>0) continue;

                        $sim = new Sim;
                        $sim->parent_agent_id = adminAgentId();
                        $sim_count++;
                        $sim->number_price = 0;
                        $sim->operator_id = $addSimByNumbers->operator;
                        $sim->tariff_id = $addSimByNumbers->tariff;
                        $sim->company_id = $addSimByNumbers->company;
                        $sim->operator_region_id = $addSimByNumbers->region;

                        $sim->number = $number;

                        $sim->save();
                        $sim->parent_id = $sim->id;
                        $sim->save();

                        Number::addNumber($sim);

                        $ids[$sim->id] = $sim->id;
                    }

                    Agent::deltaSimCount(adminAgentId(), $sim_count);

                    $transaction->commit();

                    if (empty($ids)) {
                        Yii::app()->user->setFlash('error', '<strong>Ошибка: </strong> Отсутствуют данные для добавления(возможно данные уже есть в базе)!');
                        $activeTabs['tab3'] = true;
                        $this->render('add', array('model' => $model, 'addSimByNumbers'=>$addSimByNumbers,'tariffListArray' => $tariffListArray, 'opListArray' => $opListArray, 'whereListArray' => $whereListArray, 'actMany' => $result, 'activeTabs' => $activeTabs));
                        exit;
                    }

                    if ($addSimByNumbers->where==1) {
                        $key = rand();
                        $_SESSION['moveSims'][$key] = $ids;
                        $this->redirect(array('move', 'key' => $key));
                    } else {
                        Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Данные успешно добавлены.');
                        Yii::app()->user->setFlash('tab3', true);
                        $this->refresh();
                    }
                }
            }


        }

        if (Yii::app()->user->hasFlash('tab2')) {
            $activeTabs['tab2'] = true;
        } else {
            if (Yii::app()->user->hasFlash('tab3')) {
                $activeTabs['tab3'] = true;
            } else {
                $activeTabs['tab1'] = true;
            }
        }
        $this->render('add', array('model' => $model, 'addSimByNumbers'=>$addSimByNumbers,'tariffListArray' => $tariffListArray, 'opListArray' => $opListArray, 'whereListArray' => $whereListArray, 'activeTabs' => $activeTabs));
    }


    public function actionMassSelect() {

        if ($_POST['ICCtoSelect'] != '') {
            $icc_arr = explode("\n", $_POST['ICCtoSelect']);

            foreach ($icc_arr as $icc) {
                if ($icc != '') {
                    if ($sim = Sim::model()->find('icc = "' . trim($icc) . '" or number = "' . trim($icc) . '" and parent_agent_id="'.loggedAgentId() .'"')) {
                        $ids[] = $sim->id;

                    }
                }
            }


            if (count($ids) > 0) {
                $key = rand();
                $_SESSION['moveSims'][$key] = $ids;

                $this->redirect(array('sim/move', 'key' => $key));
            } else
                Yii::app()->user->setFlash('error', '<strong>Ошибка</strong> не найдено сим в базе
            .');
        }

        $this->render('massselect');
    }

    public function actionMove($key) {
        if ($_POST['Move']) {
            if (!$_POST['Move']['agent_id']) {
                Yii::app()->user->setFlash('error', '<strong>Ошибка</strong> Не выбран агент.');
                $this->refresh();
                exit;
            }
            $totalNumberPrice = Sim::model()->getTotalNumberPrice($_SESSION['moveSims'][$key]);
            $totalSimPrice = count($_SESSION['moveSims'][$key]) * $_POST['Move']['PriceForSim'];
            if (count($_SESSION['moveSims'][$key]) == 0) {
                Yii::app()->user->setFlash('error', '<strong>Ошибка</strong> Отсутствуют данные для передачи.');
                $this->redirect(Yii::app()->createUrl('sim/add'));
                exit;
            }

            $trx = Yii::app()->db->beginTransaction();

            $model = new Act;
            $model->agent_id = $_POST['Move']['agent_id'];
            $model->dt = date('Y-m-d H:i:s', $_POST['Move']['date']);
            $model->sum = $totalNumberPrice + $totalSimPrice;
            $model->type = Act::TYPE_SIM;
            $model->save();

            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $_SESSION['moveSims'][$key]);
            $criteria->addColumnCondition(array('parent_agent_id' => loggedAgentId()));
            $criteria->addCondition('agent_id is null');
            $ids_string = implode(",", $_SESSION['moveSims'][$key]);

            // update Agent stats
            Agent::deltaSimCount($_POST['Move']['agent_id'], count($_SESSION['moveSims'][$key]));

            Sim::model()->updateAll(array('agent_id' => $_POST['Move']['agent_id'], 'act_id' => $model->id, 'sim_price' => $_POST['Move']['PriceForSim']), $criteria);

            $sql = "INSERT INTO sim (sim_price,personal_account, number,number_price, icc, parent_id, parent_agent_id, parent_act_id, agent_id, act_id, operator_id, tariff_id, operator_region_id, company_id)
              SELECT " . Yii::app()->db->quoteValue($_POST['Move']['PriceForSim']) . ", s.personal_account, s.number,s.number_price, s.icc, s.parent_id ,s.agent_id, " . Yii::app()->db->quoteValue($model->id) . ", NULL, NULL, s.operator_id, s.tariff_id, s.operator_region_id, s.company_id
              FROM sim as s
              WHERE id IN ($ids_string)";

            Yii::app()->db->createCommand($sql)->execute();

            $model->agent->recalcBalance();
            $model->agent->save();

            //add NumberHistory
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $_SESSION['moveSims'][$key]);
            $simsId = Sim::model()->findAll($criteria);
            foreach($simsId as $s) {
                $number = Number::model()->findByAttributes(array('number'=>$s->number));
                if (!empty($number)) {
                    NumberHistory::addHistoryNumber($number->id,'SIM передана агенту {Agent:'.$_POST['Move']['agent_id'].'}');
                }
            }

            $trx->commit();

            Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Данные успешно передены агенту.');
            unset($_SESSION['moveSims'][$key]);
            $this->redirect(Yii::app()->createUrl('site/index'));
        } else {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $_SESSION['moveSims'][$key]);
            $criteria->addColumnCondition(array('parent_agent_id' => loggedAgentId()));
            $criteria->addCondition('agent_id is null');
            $dataProvider = new CActiveDataProvider('Sim', array('criteria' => $criteria));

            $total = Sim::model()->getTotalNumberPrice($_SESSION['moveSims'][$key]);
            $agent = Agent::model()->getComboList();
            $agent = array(0 => Yii::t('app', 'Select Agent')) + $agent;
            $this->render('move', array('model' => $model, 'dataProvider' => $dataProvider, 'agent' => $agent, 'totalNumberPrice' => $total));
        }
    }

    public function actionAjaxcombo() {
        $tariffList = Tariff::model()->findAllByAttributes(array('operator_id' => $_POST['operatorId']));
        $res = '';
        foreach ($tariffList as $v) {
            $res .= '<option value="' . $v['id'] . '">' . $v['title'] . '</option>';
        }
        echo $res;
    }

    public function actionAjaxcombo2() {
        $regionList = OperatorRegion::getDropDownList();
        $res = '';
        if (!isset($regionList[$_POST['operatorId']])) {
            $regionList[$_POST['operatorId']]=array('' => 'Выбор региона');
        }
        foreach ($regionList[$_POST['operatorId']] as $k=>$v) {
            $res .= '<option value="' . $k . '">' . $v . '</option>';
        }
        echo $res;
    }

    public function actionUpdatePrice($id, $key)
    {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            try {
                Yii::import('bootstrap.widgets.TbEditableSaver'); //or you can add import 'ext.editable.*' to config
                $model = new TbEditableSaver('Sim'); // 'User' is classname of model to be updated
                $model->update();
                $price = Sim::model()->getTotalNumberPrice($_SESSION['moveSims'][$key]);
                echo CJSON::encode(array('price' => $price));
            } catch (CDbException $e) {
                $this->ajaxError(Yii::t("app", "Can't edit this object because it is used by another object(s)"));
            }
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    public function actionRemove($id, $key)
    {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            try {
                unset($_SESSION['moveSims'][$key][$id]);
                $price = Sim::model()->getTotalNumberPrice($_SESSION['moveSims'][$key]);
                echo CJSON::encode(array('count' => count($_SESSION['moveSims'][$key]), 'price' => $price));
            } catch (CDbException $e) {
                $this->ajaxError(Yii::t("app", "Can't delete this object because it is used by another object(s)"));
            }
            if (!Yii::app()->getRequest()->getIsAjaxRequest())
                $this->redirect(array('move', 'key' => $key));
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    public function actionFindAgent($term)
    {
        if (Yii::app()->request->isAjaxRequest && $term) {
            $agent = Agent::model()->getComboList();
            $mass = array();
            foreach ($agent as $k => $v) {
                if (strpos($v, $term) !== false) $mass[] = array('label' => $v, 'id' => $k);
            }
            echo CJSON::encode($mass);
        }
        Yii::app()->end();
    }

    public function actionList()
    {
        if (isset($_REQUEST['passSIM'])) {
            $key = rand();
            $_SESSION['moveSims'][$key] = explode(',', $_POST['ids']);

            $this->redirect(array('sim/move', 'key' => $key));
        }

        $model = new Sim('search');
        $model->unsetAttributes();

        if (isset($_GET['Sim']))
            $model->setAttributes($_GET['Sim']);

        $dataProvider = $model->search();
        $dataProvider->criteria->addColumnCondition(array('parent_agent_id' => loggedAgentId()));
        $dataProvider->criteria->with=array('tariff','operator','act','agent');

        $this->render('list', array(
            'model' => $model,
            'dataProvider' => $dataProvider
        ));
    }
}