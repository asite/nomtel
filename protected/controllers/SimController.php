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
                                if ($model->countByAttributes(array('number' => $sim[2])) == 0) {
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
                                $model->number_price = 0;
                                $model->personal_account = $sim[0];
                                $model->number = $sim[1];
                                //try {

                                if ($model->countByAttributes(array('number' => $sim[1])) == 0) {
                                    $model->save();
                                    $model->parent_id = $model->id;
                                    $model->save();
                                    $sim_count++;

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

                //if press button PROCESS
                if (isset($_POST['buttonProcessSim'])) {
                    $activeTabs['tab1'] = true;
                    $this->render('add', array('model' => $model, 'addSimByNumbers'=>$addSimByNumbers, 'tariffListArray' => $tariffListArray, 'opListArray' => $opListArray, 'whereListArray' => $whereListArray, 'actMany' => $result, 'activeTabs' => $activeTabs));
                    exit;
                //if press button add simcard
                } else {
                    $sim_count = 0;
                    foreach ($result as $v) {
                        $v->parent_agent_id = adminAgentId();
                        $sim_count++;
                        $v->operator_id = $_POST['AddSim']['operator'];
                        $v->tariff_id = $_POST['AddSim']['tariff'];
                        $v->save();

                        Number::addNumber($v);

                        $ids[$v->id] = $v->id;
                    }
                    Agent::deltaSimCount(adminAgentId(), $sim_count);

                    if (empty($result)) {
                        Yii::app()->user->setFlash('error', '<strong>Ошибка: </strong> Отсутствуют данные для добавления!');
                        $activeTabs['tab1'] = true;
                        $this->render('add', array('model' => $model, 'tariffListArray' => $tariffListArray, 'opListArray' => $opListArray, 'whereListArray' => $whereListArray, 'actMany' => $result, 'activeTabs' => $activeTabs));
                        exit;
                    }

                    //if simcard move to agent
                    if ($_POST['AddSim']['where']) {

                        $sessionData=new SessionData(__CLASS__);
                        $key=$sessionData->add($ids);

                        $transaction->commit();

                        $this->redirect(array('move', 'key' => $key));
                    //if simcard move to base
                    } else {
                        Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Данные успешно добавлены.');
                        $transaction->commit();
                        $this->refresh();
                        exit;
                    }
                }
            }

            //add few simcard in base or in base and to agent
            if ($_POST['simMethod'] == 'add-few-sim') {
                if (Yii::app()->getRequest()->getIsAjaxRequest()) {
                    $result = $this->validate($model, $_POST['AddSim']);
                    echo function_exists('json_encode') ? json_encode($result) : CJSON::encode($result);
                    Yii::app()->end();
                }
                $old_model = $model;

                $sim_count = 0;

                for ($o = 0; $o <= count($_POST['AddSim'])-1; $o++) {
                    if (isset($_POST['AddSim'][$o]['phone']) && ($_POST['AddSim'][$o]['phone']))
                    {
                        $model = new Sim;
                        $model->parent_agent_id = adminAgentId();
                        $model->number_price = 0;
                        $model->operator_id = $_POST['AddSim'][0]['operator'];
                        $model->tariff_id = $_POST['AddSim'][0]['tariff'];
                        $model->personal_account = $_POST['AddSim'][$o]['ICCPersonalAccount'];
                        $model->icc = $_POST['AddSim'][$o]['ICCBeginFew'] . $_POST['AddSim'][$o]['ICCEndFew'];
                        $model->number = $_POST['AddSim'][$o]['phone'];
                        $model->operator_region_id=$_POST['AddSim'][0]['region'];
                        $model->company_id=$_POST['AddSim'][0]['company'];

                        if ($model->countByAttributes(array('number' => $_POST['AddSim'][$o]['phone'])) == 0) {
                            //try {
                            $model->save();
                            $model->parent_id = $model->id;
                            $model->save();
                            $sim_count++;

                            Number::addNumber($model);

                            $ids[$model->id] = $model->id;

                            //} catch(Exception $e) {}
                        }
                    }
                }

                Agent::deltaSimCount(adminAgentId(), $sim_count);

                if (empty($ids)) {
                    Yii::app()->user->setFlash('error', '<strong>Ошибка: </strong> Отсутствуют данные для добавления(возможно данные уже есть в базе)!');
                    $activeTabs['tab2'] = true;
                    $this->render('add', array(
                        'model' => $old_model,
                        'tariffListArray' => $tariffListArray,
                        'opListArray' => $opListArray,
                        'whereListArray' => $whereListArray,
                        'actMany' => $result,
                        'activeTabs' => $activeTabs,
                        'addSimByNumbers'=>$addSimByNumbers
                        )
                    );
                    exit;
                }

                //if simcard move to agent
                if ($_POST['AddSim'][0]['where']) {
                    $sessionData=new SessionData(__CLASS__);
                    $key=$sessionData->add($ids);

                    $transaction->commit();
                    $this->redirect(array('move', 'key' => $key));
                    exit;
                 //if simcard move to base
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
                        $sessionData=new SessionData(__CLASS__);
                        $key=$sessionData->add($ids);

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
                    if ($sim = Sim::model()->find('(icc = "' . trim($icc) . '" or number = "' . trim($icc) . '") and parent_agent_id="'.loggedAgentId() .'"')) {
                        $ids[$sim->id] = $sim->id;
                    }
                }
            }



            if (count($ids) > 0) {
                $sessionData=new SessionData(__CLASS__);
                $key=$sessionData->add($ids);

                $this->redirect(array('sim/move', 'key' => $key));
            } else
                Yii::app()->user->setFlash('error', '<strong>Ошибка</strong> не найдено сим в базе
            .');
        }

        $this->render('massselect');
    }

    public function actionMove($key) {
        $sessionData=new SessionData(__CLASS__);
        $moveSimCards=$sessionData->get($key);
        $countMoveSimCards=count($moveSimCards);

        if ($_POST['Move']) {
            $agent_id = $_POST['Act']['agent_id'];
            if ($agent_id == 0) $_POST['Act']['agent_id']='';
            $model = new Act;
            $this->performAjaxValidation($model, 'move-sim');

            $trx = Yii::app()->db->beginTransaction();

            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $moveSimCards);
            $criteria->addColumnCondition(array('parent_agent_id' => loggedAgentId()));
            $criteria->addCondition('agent_id is null');

            $idsToMove=Yii::app()->db->createCommand("select id from sim where ".$criteria->condition)->queryColumn($criteria->params);
            $ids_string = implode(",", $idsToMove);

            $totalNumberPrice = Sim::model()->getTotalNumberPrice($idsToMove);
            $totalSimPrice = $countMoveSimCards * $_POST['Move']['PriceForSim'];
            if ($countMoveSimCards == 0) {
                $trx->rollback();
                Yii::app()->user->setFlash('error', '<strong>Ошибка</strong> Отсутствуют данные для передачи.');
                $this->redirect(Yii::app()->createUrl('sim/add'));
                exit;
            }

            $model = new Act;
            $model->agent_id = $agent_id;
            $model->dt = date('Y-m-d H:i:s', $_POST['Move']['date']);
            $model->sum = $totalNumberPrice + $totalSimPrice;
            $model->type = Act::TYPE_SIM;
            $model->save();

            // update Agent stats
            Agent::deltaSimCount($agent_id, $countMoveSimCards);

            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $idsToMove);

            Sim::model()->updateAll(array('agent_id' => $agent_id, 'act_id' => $model->id, 'sim_price' => $_POST['Move']['PriceForSim']), $criteria);

            $sql = "INSERT INTO sim (sim_price,personal_account, number,number_price, icc, parent_id, parent_agent_id, parent_act_id, agent_id, act_id, operator_id, tariff_id, operator_region_id, company_id)
              SELECT " . Yii::app()->db->quoteValue($_POST['Move']['PriceForSim']) . ", s.personal_account, s.number,s.number_price, s.icc, s.parent_id ,s.agent_id, " . Yii::app()->db->quoteValue($model->id) . ", NULL, NULL, s.operator_id, s.tariff_id, s.operator_region_id, s.company_id
              FROM sim as s
              WHERE id IN ($ids_string)";

            Yii::app()->db->createCommand($sql)->execute(array(':parent_agent_id'=>loggedAgentId()));

            $model->agent->recalcBalance();
            $model->agent->save();

            //add NumberHistory
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $moveSimCards);
            $simsId = Sim::model()->findAll($criteria);
            foreach($simsId as $s) {
                $number = Number::model()->findByAttributes(array('number'=>$s->number));
                if (!empty($number)) {
                    NumberHistory::addHistoryNumber($number->id,'SIM передана агенту {Agent:'.$agent_id.'} по акту {Act:'.$model->id.'}');
                }
            }

            $trx->commit();

            Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Данные успешно передены агенту.');
            $sessionData->delete($key);
            $this->redirect(Yii::app()->createUrl('site/index'));
        } else {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $moveSimCards);
            $criteria->addColumnCondition(array('parent_agent_id' => loggedAgentId()));
            $criteria->addCondition('agent_id is null');
            $dataProvider = new CActiveDataProvider('Sim', array('criteria' => $criteria));

            $act = new Act;
            $total = Sim::model()->getTotalNumberPrice($moveSimCards);
            $agent = Agent::model()->getComboList();
            $agent = array('0'=>Yii::t('app', 'Select Agent')) + $agent;
            $this->render('move', array('model' => $model, 'dataProvider' => $dataProvider, 'agent' => $agent, 'totalNumberPrice' => $total, 'act'=>$act));
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

    public function actionUpdatePrice($key) {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            try {
                $sessionData=new SessionData(__CLASS__);
                $data=$sessionData->get($key);

                $sim = Sim::model()->findByPk($_REQUEST['pk']);
                $sim->number_price = $_REQUEST['value'];
                $sim->save();

                //$criteria = new CDbCriteria();
                //$criteria->addCondition('parent_id=:id');
                //$criteria->params = array(":id" => $sim->id);
                //Sim::model()->updateAll(array('number_price' => $_REQUEST['value']), $criteria);

                //Yii::import('bootstrap.widgets.TbEditableSaver'); //or you can add import 'ext.editable.*' to config
                //$model = new TbEditableSaver('Sim'); // 'User' is classname of model to be updated
                //$model->update();

                $price = Sim::model()->getTotalNumberPrice($data);
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
                $sessionData=new SessionData(__CLASS__);
                $data=$sessionData->get($key);
                unset($data[$id]);
                $sessionData->set($key,$data);

                $price = Sim::model()->getTotalNumberPrice($data);
                echo CJSON::encode(array('count' => count($data), 'price' => $price));
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

    private static function getSimListDataProvider($pager=true) {
        $model = new SimSearch();
        $model->unsetAttributes();

        if (isset($_REQUEST['SimSearch']))
            $model->setAttributes($_REQUEST['SimSearch']);

        $criteria = new CDbCriteria();
        $criteria->compare('s.is_active',1);
        $criteria->compare('s.parent_agent_id',loggedAgentId());

        if ($model->agent_id !== '0')
            $criteria->compare('s.agent_id', $model->agent_id);
        else
            $criteria->addCondition("s.agent_id is null");

        if ($model->number != Yii::t('app', 'WITHOUT NUMBER'))
            $criteria->compare('s.number', $model->number, true);
        else
            $criteria->addCondition("(s.number='' or s.number is null)");

        if ($model->support_status != '0')
            $criteria->compare('n.support_status', $model->support_status);
        else
            $criteria->addCondition("n.support_status is null");

        if ($model->support_operator_id != '0')
            $criteria->compare('n.support_operator_id', $model->support_operator_id);
        else
            $criteria->addCondition("n.support_operator_id is null");

        $criteria->compare('s.icc',$model->icc);
        $criteria->compare('s.operator_id',$model->operator_id);
        $criteria->compare('s.tariff_id',$model->tariff_id);
        $criteria->compare('n.status',$model->status);
        $criteria->compare('n.balance_status',$model->balance_status);

        $sql = "from sim s
            left outer join number n on (s.parent_id=n.sim_id)
            left outer join agent a on (a.id=s.agent_id)
            left outer join operator o on (o.id=s.operator_id)
            left outer join support_operator so on (so.id=n.support_operator_id)
            left outer join tariff t on (t.id=s.tariff_id)
            where " . $criteria->condition;

        $totalItemCount = Yii::app()->db->createCommand('select count(*) ' . $sql)->queryScalar($criteria->params);

        $dataProvider = new CSqlDataProvider('select s.*,n.*,s.number,o.title as operator,t.title as tariff, a.name, a.surname,s.id as sim_id,n.id as number_id,n.status as number_status,so.name as so_name,so.surname as so_surname ' . $sql, array(
            'totalItemCount' => $totalItemCount,
            'params' => $criteria->params,
            'sort' => array(
                'attributes' => array(
                    'agent_id','number','icc','operator_id','tariff_id','status','balance_status','support_status','support_operator_id'
                ),
            ),
            'pagination' => $pager?array('pageSize' => Sim::ITEMS_PER_PAGE):false
        ));
        $list['model'] = $model;
        $list['dataProvider'] = $dataProvider;
        return $list;
    }

    public function actionList()
    {
        if (isset($_REQUEST['passSIM'])) {
            $sessionData=new SessionData(__CLASS__);
            $data=array();
            foreach(explode(',', $_POST['ids']) as $v)
                if ($v!='') $data[$v]=$v;
            $key=$sessionData->add($data);

            $this->redirect(array('sim/move', 'key' => $key));
        }

        $list = self::getSimListDataProvider();
        /*if (isset($_REQUEST['exportExel'])) {
            $columns = array(
                'agent' => array('name'=>Yii::t('app','Agent'),'value'=>'$data["name"]." ".$data["surname"]'),
                'number' => array('name'=>Yii::t('app','Number'),'value'=>'$data["number"]'),
                'ICC' => array('name'=>Yii::t('app','ICC'),'value'=>'$data["icc"]'),
                'operator' => array('name'=>Yii::t('app','Operator'),'value'=>'$data["operator"]'),
                'tariff' => array('name'=>Yii::t('app','Tariff'),'value'=>'$data["tariff"]'),
                'status' => array('name'=>Yii::t('app','Status'),'value'=>'Number::getStatusLabel($data["status"])'),
                'balance_status' => array('name'=>Yii::t('app','Balance Status'),'value'=>'Number::getBalanceStatusLabel($data["balance_status"])')
            );
            $this->exportExel($list['dataProvider'], $columns);
        }*/

        $this->render('list', array(
            'model' => $list['model'],
            'dataProvider' => $list['dataProvider']
        ));
    }

}