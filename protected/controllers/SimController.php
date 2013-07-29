<?php

class SimController extends BaseGxController {

    public function additionalAccessRules() {
        return array(
            array('disallow', 'actions' => array('massMove'), 'roles' => array('agent')),
            array('allow', 'actions' => array(), 'roles' => array('agent')),
        );
    }

    public function filters()
    {
        return array_merge(parent::filters(), array(
            array('LoggingFilter +massMove')
        ));
    }

    protected function performAjaxValidation($model, $id = '') {
        if (isset($_POST['ajax']) && $_POST['ajax'] === $id) {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionDelivery() {
        if (Yii::app()->user->role=='agent' && !isKrylow()) $this->throw403();

        $activeTabs = array('tab1' => false, 'tab2' => false,'tab3' => false);
        $model = new Sim;

        $companyListArray = Company::getDropDownList();
        $regionListArray = OperatorRegion::getDropDownList();
        $tariffListArray = Tariff::getDropDownList();
        $agentListArray = array('0'=>'БАЗА')+Agent::getComboList();

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

                        $sim = $ids = array();
                        $sim_count = 0;
                        for ($row = 1; $row <= $highestRow; $row++) {
                            /*for ($col = 0; $col <= $highestColumnIndex; ++$col) {
                                $info = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                                if ($col == 0 && $info != '') $sim[0] = $info;
                                if (preg_match('%- (\d{10})$%', $info, $matches)) $sim[1] = $matches[1];
                            }*/
                            $sim[0] = $objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
                            $sim[1] = $objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
                            $sim[2] = $objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
                            if ($sim[1]) {
                                $model = new Sim;
                                $model->setAttributes($data);
                                $model->agent_id = NULL;
                                $model->parent_agent_id = adminAgentId();
                                $model->number_price = 0;
                                $model->personal_account = $sim[0];
                                $model->number = $sim[1];
                                $model->icc = $sim[2];
                                //try {

                                if ($model->countByAttributes(array('number' => $sim[1])) == 0) {
                                    $model->save();
                                    $model->parent_id = $model->id;
                                    $model->save();
                                    $sim_count++;

                                    Number::addNumber($model);

                                    $sims[$i]['id'] = $model->id;
                                    $sims[$i]['personal_account'] = $sim[0];
                                    $sims[$i]['icc'] = $sim[2];
                                    $sims[$i++]['number'] = $sim[1];
                                    $ids[$model->id]=$model->id;
                                    //} catch(Exception $e) { }
                                }
                            }
                        }

                        if ($data['agent_id']) {

                            $transaction->commit();

                            $sessionData=new SessionData(__CLASS__);
                            $key=$sessionData->add($ids);

                            $this->redirect(array('move', 'key' => $key));
                        }
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
        $this->render('delivery', array(
            'model' => $model,
            'activeTabs' => $activeTabs,
            'company' => $companyListArray,
            'regionList' => $regionListArray,
            'tariffList' => $tariffListArray,
            'agentList' => $agentListArray,
        ));
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
        if (Yii::app()->user->role=='agent' && !isKrylow()) $this->throw403();

        $model = new AddSim;
        $addSimByNumbers=new AddSimByNumbers();

        $opListArray = Operator::getComboList();
        if (isKrylow()) {
            foreach($opListArray as $k=>$v)
                if ($k!=Operator::OPERATOR_BEELINE_ID) unset($opListArray[$k]);
        }

        if (isset($_POST['AddSim']['operator'])) $operator_id = $_POST['AddSim']['operator']; else $operator_id = key($opListArray);
        if (isKrylow() && $operator_id!=Operator::OPERATOR_BEELINE_ID) $this->throw403();

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

                    if (empty($result)) {
                        Yii::app()->user->setFlash('error', '<strong>Ошибка: </strong> Отсутствуют данные для добавления!');
                        $activeTabs['tab1'] = true;
                        $this->render('add', array('model' => $model, 'addSimByNumbers'=>$addSimByNumbers,'tariffListArray' => $tariffListArray, 'opListArray' => $opListArray, 'whereListArray' => $whereListArray, 'activeTabs' => $activeTabs));
                        //$this->render('add', array('model' => $model, 'tariffListArray' => $tariffListArray, 'opListArray' => $opListArray, 'whereListArray' => $whereListArray, 'actMany' => $result, 'activeTabs' => $activeTabs));
                        exit;
                    }

                    //if simcard move to agent
                    if ($_POST['AddSim']['where'] && !isKrylow()) {

                        $sessionData=new SessionData(__CLASS__);
                        $key=$sessionData->add($ids);

                        $transaction->commit();

                        $this->redirect(array('move', 'key' => $key));
                    //if simcard move to base
                    } else {
                        if (isKrylow()) $this->move($ids,krylowAgentId(),adminAgentId(),true);
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
                if ($_POST['AddSim'][0]['where'] && !isKrylow()) {
                    $sessionData=new SessionData(__CLASS__);
                    $key=$sessionData->add($ids);

                    $transaction->commit();
                    $this->redirect(array('move', 'key' => $key));
                    exit;
                 //if simcard move to base
                } else {
                    if (isKrylow()) $this->move($ids,krylowAgentId(),adminAgentId(),true);
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
                if (isKrylow()) $addSimByNumbers->where=2;

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

                    $transaction->commit();

                    if (empty($ids)) {
                        Yii::app()->user->setFlash('error', '<strong>Ошибка: </strong> Отсутствуют данные для добавления(возможно данные уже есть в базе)!');
                        $activeTabs['tab3'] = true;
                        $this->render('add', array('model' => $model, 'addSimByNumbers'=>$addSimByNumbers,'tariffListArray' => $tariffListArray, 'opListArray' => $opListArray, 'whereListArray' => $whereListArray, 'actMany' => $result, 'activeTabs' => $activeTabs));
                        exit;
                    }

                    if ($addSimByNumbers->where==1 && !isKrylow()) {
                        $sessionData=new SessionData(__CLASS__);
                        $key=$sessionData->add($ids);

                        $this->redirect(array('move', 'key' => $key));
                    } else {
                        if (isKrylow()) $this->move($ids,krylowAgentId(),adminAgentId(),true);
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
            $id_arr = explode("\n", $_POST['ICCtoSelect']);

            if (!empty($id_arr)) {
                $quotedIds=array();
                foreach($id_arr as $id) $quotedIds[]=Yii::app()->db->quoteValue(trim($id));
                $in='('.implode(',',$quotedIds).')';
                $sql = "select id from sim where (number in $in or icc in $in) and agent_id is NULL and parent_agent_id='".loggedAgentId()."' and is_active=1";
                $sims=Yii::app()->db->createCommand($sql)->queryAll();
            } else {
                $sims=array();
            }

            $ids = array();
            foreach ($sims as $value) {
                $ids[$value['id']]=$value['id'];
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

    private function move($moveSimCards,$agent_id,$source_agent_id=null,$force_zero_sum=false) {
        $countMoveSimCards=count($moveSimCards);

        if (!$source_agent_id) $source_agent_id=loggedAgentId();
        if ($agent_id==loggedAgentId()) {
            $agent=Agent::model()->findByPk(loggedAgentId());
            $source_agent_id=$agent->parent_id;
        }

        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', $moveSimCards);
        $criteria->addColumnCondition(array('parent_agent_id' => $source_agent_id));
        $criteria->addCondition('agent_id is null');

        $idsToMove=Yii::app()->db->createCommand("select id from sim where ".$criteria->condition)->queryColumn($criteria->params);
        $ids_string = implode(",", $idsToMove);

        $totalNumberPrice = Sim::model()->getTotalNumberPrice($idsToMove);
        $totalSimPrice = $countMoveSimCards * $_POST['Move']['PriceForSim'];
        if ($countMoveSimCards == 0) return false;

        $model = new Act;
        $model->agent_id = $agent_id;
        $model->dt = new EDateTime();//date('Y-m-d H:i:s', $_POST['Move']['date']);

        if (!$force_zero_sum)
            $model->sum = $totalNumberPrice + $totalSimPrice;
        else
            $model->sum =0 ;

        $model->type = Act::TYPE_SIM;
        $model->save();

        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', $idsToMove);

        Sim::model()->updateAll(array('agent_id' => $agent_id, 'act_id' => $model->id, 'sim_price' => $_POST['Move']['PriceForSim']), $criteria);

        $sql = "INSERT INTO sim (sim_price,personal_account, number,number_price, icc, parent_id, parent_agent_id, parent_act_id, agent_id, act_id, operator_id, tariff_id, operator_region_id, company_id)
              SELECT " . Yii::app()->db->quoteValue($_POST['Move']['PriceForSim']) . ", s.personal_account, s.number,s.number_price, s.icc, s.parent_id ,s.agent_id, " . Yii::app()->db->quoteValue($model->id) . ", NULL, NULL, s.operator_id, s.tariff_id, s.operator_region_id, s.company_id
              FROM sim as s
              WHERE id IN ($ids_string)";

        Yii::app()->db->createCommand($sql)->execute(array(':parent_agent_id'=>$source_agent_id));

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

        return true;
    }

    public function actionMove($key) {
        $sessionData=new SessionData(__CLASS__);
        $moveSimCards=$sessionData->get($key);

        if (isset($_REQUEST['Act']['agent_id'])) {
            $_POST['Act']['agent_id']=$_REQUEST['Act']['agent_id'];
            $force_agent_id=$_REQUEST['Act']['agent_id'];
        }
        $agent_id = $_POST['Act']['agent_id'];
        if ($agent_id == 0) $_POST['Act']['agent_id']='';

        if ($_POST['Move']) {
            $model = new Act;
            $this->performAjaxValidation($model, 'move-sim');

            $trx = Yii::app()->db->beginTransaction();

            if (!$this->move($moveSimCards,$agent_id)) {
                Yii::app()->user->setFlash('error', '<strong>Ошибка</strong> Отсутствуют данные для передачи.');
                $this->redirect(Yii::app()->createUrl('sim/add'));
                exit;
            }

            $trx->commit();

            Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Данные успешно передены агенту.');
            $sessionData->delete($key);
            $this->redirect(Yii::app()->createUrl('site/index'));
        } else {
            if ($agent_id==loggedAgentId()) {
                $agent=Agent::model()->findByPk(loggedAgentId());
                $source_agent_id=$agent->parent_id;
            } else {
                $source_agent_id=loggedAgentId();
            }

            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $moveSimCards);
            $criteria->addColumnCondition(array('parent_agent_id' => $source_agent_id));
            $criteria->addCondition('agent_id is null');
            $dataProvider = new CActiveDataProvider('Sim', array('criteria' => $criteria));

            $act = new Act;
            $total = Sim::model()->getTotalNumberPrice($moveSimCards);
            $agents = Agent::model()->getComboList();
            $agents = array('0'=>Yii::t('app', 'Select Agent')) + $agents;
            $this->render('move', array('force_agent_id'=>$force_agent_id,'model' => $model, 'dataProvider' => $dataProvider, 'agent' => $agents, 'totalNumberPrice' => $total, 'act'=>$act));
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

        $criteria->compare('s.icc',$model->icc,true);
        $criteria->compare('s.operator_id',$model->operator_id);
        $criteria->compare('s.tariff_id',$model->tariff_id);
        $criteria->compare('s.operator_region_id',$model->operator_region_id);
        $criteria->compare('n.status',$model->status);
        $criteria->compare('n.balance_status',$model->balance_status);
        $criteria->compare('n.number_city',$model->number_city);

        $sql = "from sim s
            left outer join number n on (s.parent_id=n.sim_id)
            left outer join agent a on (a.id=s.agent_id)
            left outer join operator o on (o.id=s.operator_id)
            left outer join support_operator so on (so.id=n.support_operator_id)
            left outer join tariff t on (t.id=s.tariff_id)
            left outer join operator_region r on (r.id=s.operator_region_id)
            where " . $criteria->condition;

        $totalItemCount = Yii::app()->db->createCommand('select count(*) ' . $sql)->queryScalar($criteria->params);

        $dataProvider = new CSqlDataProvider('select s.*,n.*,s.number,o.title as operator,t.title as tariff,r.title as operator_region, a.name, a.surname,s.id as sim_id,n.id as number_id,n.status as number_status,n.number_city,so.name as so_name,so.surname as so_surname ' . $sql, array(
            'totalItemCount' => $totalItemCount,
            'params' => $criteria->params,
            'sort' => array(
                'attributes' => array(
                    'agent_id','number','icc','operator_id','tariff_id','operator_region_id','status','balance_status','number_city','support_operator_id'
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

        if (isset($_REQUEST['activeSIM'])) {
            $data=array();
            foreach(explode(',', $_POST['ids']) as $v)  if ($v!='') $data[$v]=$v;

            $trx=Yii::app()->db->beginTransaction();

            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $data);

            $simcards = Sim::model()->findAll($criteria);

            foreach ($simcards as $s) {
                $number = Number::model()->findByAttributes(array('sim_id'=>$s->parent_id));
                $number->status = Number::STATUS_ACTIVE;
                $number->save();
                NumberHistory::addHistoryNumber($number->id,'Номер подключен.');

            }
            $trx->commit();
             Yii::app()->user->setFlash('success','Сим карты успешно подключены.');
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

    public function actionMassMove() {
        if ($_POST['ICCtoMove'] != '') {
            $id_arr = explode("\n", $_POST['ICCtoMove']);

            $trx=Yii::app()->db->beginTransaction();

            $st=microtime(true);

            $recursiveInfo=array();
            $destAgent=Agent::model()->findByPk($_POST['agent_id']);
            if (!$destAgent) {
                Yii::app()->user->setFlash('error','Выберите агента для передачи SIM');
                $this->refresh();
            }

            $agent=$destAgent;
            while($agent) {
                if ($agent->id==adminAgentId()) break;
                $act=new Act;
                $act->agent_id=$agent->id;
                $act->type=Act::TYPE_SIM;
                $act->sum=0;
                $act->comment='Массовая передача симкарт агенту "'.$destAgent.'"';
                $act->dt=new EDateTime();
                $act->save();
                $recursiveInfo[]=array('agent'=>$agent,'act'=>$act);
                $agent=$agent->parent;
            }
            $recursiveInfo=array_reverse($recursiveInfo);
            $recursiveInfo[]=array('agent'=>new Agent,'act'=>new Act);

            $resOK=array();

            $simIdsToUpdateParentId=array();
            $simParentIdsToSetInactive=array();

            $simAttrs=Sim::model()->attributes;
            unset($simAttrs['id']);
            $simBulkInsert=new BulkInsert('sim',array_keys($simAttrs));

            $updateSimInNumberCommand=Yii::app()->db->createCommand('update number set sim_id=:new_sim_id where sim_id=:old_sim_id');

            $numberSimIds=array();

            foreach($id_arr as $id) {
                $id=trim($id);
                if ($id!='') $resNotFound[trim($id)]=true;
            }

            if (!empty($id_arr)) {
                $quotedIds=array();
                foreach($id_arr as $id) $quotedIds[]=Yii::app()->db->quoteValue(trim($id));
                $in='('.implode(',',$quotedIds).')';
                $sims=Sim::model()->findAllBySql("select * from sim where (number in $in or icc in $in) and id=parent_id and is_active=1");
            } else {
                $sims=array();
            }

            foreach($sims as $sim) {
                unset($resNotFound[$sim->number]);
                unset($resNotFound[$sim->icc]);

                $numberSimId=$sim->parent_id;

                if (!$sim) {
                    $resNotFound[]=$id;
                    continue;
                }

                $simParentIdsToSetInactive[]=$sim->id;

                $parentAgentId=adminAgentId();
                $parentActId=null;
                $parentSimId=null;
                foreach($recursiveInfo as $rInfo) {
                    $sim->isNewRecord=true;
                    $sim->id=null;

                    $sim->parent_agent_id=$parentAgentId;
                    $sim->parent_act_id=$parentActId;

                    $sim->agent_id=$rInfo['agent']->id;
                    $sim->act_id=$rInfo['act']->id;


                    if (!$parentSimId) {
                        $sim->save();
                        $sim->parent_id=$sim->id;
                        $parentSimId=$sim->id;

                        $simIdsToUpdateParentId[]=$sim->id;
                    } else {
                        $simBulkInsert->insert($sim->attributes);
                    }

                    $parentAgentId=$rInfo['agent']->id;
                    $parentActId=$rInfo['act']->id;
                }

                $updateSimInNumberCommand->execute(array(':new_sim_id'=>$parentSimId,':old_sim_id'=>$numberSimId));
                $numberSimIds[]=$parentSimId;

                $resOK[]=$id;
            }

            $simBulkInsert->finish();

            $numberHistoryBulkInsert=new BulkInsert('number_history',array('number_id','who','comment'));
            $numberHistory=array('who'=>NumberHistory::getDefaultWho(),'comment'=>"Массовая передача сим агенту {Agent:{$destAgent->id}}");

            if (!empty($numberSimIds)) {
                $ids=Yii::app()->db->createCommand("select id from number where sim_id in (".implode(',',$numberSimIds).')')->queryColumn();
                foreach($ids as $id) {
                    $numberHistory['number_id']=$id;
                    $numberHistoryBulkInsert->insert($numberHistory);
                }
            }

            $numberHistoryBulkInsert->finish();

            if (!empty($simIdsToUpdateParentId))
                Yii::app()->db->createCommand("update sim set parent_id=id where id in (".implode(',',$simIdsToUpdateParentId).')')->execute();

            if (!empty($simParentIdsToSetInactive))
                Yii::app()->db->createCommand("update sim set is_active=0 where parent_id in (".implode(',',$simParentIdsToSetInactive).')')->execute();

            $trx->commit();

            if (!empty($resNotFound)) {
                $res='<b>следующие ICC/Номера не найдены:</b> '.implode(',',array_keys($resNotFound));
            } else {
                $res='Все SIM успешно переданы';
            }

            Yii::app()->user->setFlash('success',$res);
            $this->refresh();
        }

        $agent = Agent::model()->getFullComboList();
        $agent = array('0'=>Yii::t('app', 'Select Agent')) + $agent;

        $this->render('massmove',array('agent'=>$agent));
    }

}