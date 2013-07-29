<?php

class NumberController extends BaseGxController
{
    public function additionalAccessRules() {
        return array(
            array('allow', 'actions' => array('list','view','agentChangeIcc'), 'roles' => array('agent')),
            array('allow', 'roles' => array('editNumberCard')),
            array('allow', 'roles' => array('support','cashier')),
        );
    }

    public function actionList()
    {
        $model = new Number('list');
        $model->unsetAttributes();

        if (isset($_GET['Number']))
            $model->setAttributes($_GET['Number']);

        $this->render('list', array(
            'model' => $model,
            'dataProvider' => $model->search()
        ));
    }

    private static function getNumberInfo($id) {
        $params = array();

        $params['number']=self::loadModel($id,'Number');

        $criteria = new CDbCriteria();
        $criteria->condition = "parent_id=:sim_id";
        $criteria->params= array(":sim_id" => $params['number']->sim_id);
        $criteria->order = "id DESC";
        $criteria->limit = 1;
        $params['sim'] = Sim::model()->find($criteria);

        $criteria = new CDbCriteria();
        $criteria->condition = "number_id=:id";
        $criteria->params= array(":id" => $params['number']->id);
        $criteria->order = "id DESC";
        $criteria->limit = 1;
        $params['SubscriptionAgreement'] = SubscriptionAgreement::model()->find($criteria);

        $criteria = new CDbCriteria();
        $criteria->condition = "number_id=:id";
        $criteria->params= array(":id" => $params['number']->id);
        $criteria->order = "id DESC";
        $criteria->limit = 1;
        $params['BonusReportNumber'] = BonusReportNumber::model()->find($criteria);

        $params['numberHistory'] = new NumberHistory('search');
        $params['numberHistory']->number_id = $params['number']->id;

        $params['numberLastInfo']=NumberLastInfo::model()->findByPk($params['number']->id);

        $sql="from balance_report_number brn
              join balance_report br on (br.id=brn.balance_report_id)
              where brn.number_id=:number_id";
        $sqlParams=array(':number_id'=>$params['number']->id);
        $totalItemCount=Yii::app()->db->createCommand("select count(*) $sql")->queryScalar($sqlParams);

        $params['balancesDataProvider'] = new CSqlDataProvider("select br.dt,brn.balance $sql order by dt desc", array(
            'totalItemCount' => $totalItemCount,
            'params' => $sqlParams,
            'pagination' => array('pageSize' => 10)
        ));

        $criteria = new CDbCriteria();
        $criteria->addCondition("number_id=:number_id");
        $criteria->params = array(':number_id'=>$params['number']->id);

        $params['ticketsDataProvider'] = new CActiveDataProvider('Ticket', array('criteria' => $criteria,'sort'=>array('defaultOrder'=>'id desc')));

        return $params;
    }

    public function addTicket($id) {
        $form=new AddTicketForm();
        $this->performAjaxValidation($form,'add-ticket');
        if (isset($_POST['AddTicketForm'])) {
            $form->setAttributes($_POST['AddTicketForm']);
            if ($form->validate()) {
                $trx=Yii::app()->db->beginTransaction();

                $tid=Ticket::addMessage($id,$form->text);
                $ticket = Ticket::model()->findByPk($tid);
                $ticket->status = Ticket::STATUS_IN_WORK_MEGAFON;
                $ticket->internal=$ticket->text;
                $ticket->sendMegafonNotification();
                $ticket->save();

                $trx->commit();
                Yii::app()->user->setFlash('success','Тикет успешно отправлен в мегафон');
                $this->refresh();
            }
        }
        return $form;
    }

    public function actionView($id)
    {
        $params = $this->getNumberInfo($id);
        $params['addTicket']=$this->addTicket($id);

        $this->render('view',$params);
    }

    public function actionEdit($id)
    {
        $params = $this->getNumberInfo($id);
        $params['addTicket']=$this->addTicket($id);

        $this->render('edit',$params);
    }

    public function actionSaveTariff($id) {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            try {
                $number = Number::model()->findByPk($id);
                $tariff_id = Sim::model()->findByAttributes(array('parent_id' => $number->sim_id));
                $criteria = new CDbCriteria();
                $criteria->addCondition('parent_id=:id');
                $criteria->params = array(
                    ':id' => $number->sim_id
                );
                Sim::model()->updateAll(array('tariff_id' => $_POST['value']), $criteria);
                if ($tariff_id->tariff_id)
                    NumberHistory::addHistoryNumber($number->id,'Тариф сменен с {Tariff:'.$tariff_id->tariff_id.'} на {Tariff:'.$_POST['value'].'}');
                else
                    NumberHistory::addHistoryNumber($number->id,'Установлен тариф {Tariff:'.$_POST['value'].'}');
            } catch (CDbException $e) {
                $this->ajaxError(Yii::t("app", "Can't delete this object because it is used by another object(s)"));
            }
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    public function actionSaveOperatorRegion($id) {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            try {
                $number = Number::model()->findByPk($id);
                $region_id = Sim::model()->findByAttributes(array('parent_id' => $number->sim_id));
                $criteria = new CDbCriteria();
                $criteria->addCondition('parent_id=:id');
                $criteria->params = array(
                    ':id' => $number->sim_id
                );
                Sim::model()->updateAll(array('operator_region_id' => $_POST['value']), $criteria);
                if ($region_id->operator_region_id)
                  NumberHistory::addHistoryNumber($number->id,'Регион сменен с {OperatorRegion:'.$region_id->operator_region_id.'} на {OperatorRegion:'.$_POST['value'].'}');
                else
                  NumberHistory::addHistoryNumber($number->id,'Установлен регион {OperatorRegion:'.$_POST['value'].'}');
            } catch (CDbException $e) {
                $this->ajaxError(Yii::t("app", "Can't delete this object because it is used by another object(s)"));
            }
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    public function actionSaveCompany($id) {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            try {
                $number = Number::model()->findByPk($id);
                $company_id = Sim::model()->findByAttributes(array('parent_id' => $number->sim_id));
                $criteria = new CDbCriteria();
                $criteria->addCondition('parent_id=:id');
                $criteria->params = array(
                    ':id' => $number->sim_id
                );
                Sim::model()->updateAll(array('company_id' => $_POST['value']), $criteria);
                if ($company_id->company_id)
                    NumberHistory::addHistoryNumber($number->id,'Компания сменена с {Company:'.$company_id->company_id.'} на {Company:'.$_POST['value'].'}');
                else
                    NumberHistory::addHistoryNumber($number->id,'Установлена компания {Company:'.$_POST['value'].'}');
            } catch (CDbException $e) {
                $this->ajaxError(Yii::t("app", "Error"));
            }
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    public function actionSaveCodeword($id) {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            try {
                $number = Number::model()->findByPk($id);
                $codeword = $number->codeword;
                $number->codeword = $_POST['value'];
                $number->save();
                NumberHistory::addHistoryNumber($number->id,'Кодовое слово сменено с "'.$codeword.'" на "'.$_POST['value'].'"');
            } catch (CDbException $e) {
                $this->ajaxError(Yii::t("app", "Error"));
            }
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    public function actionSaveSimPrice($id) {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            try {
                $number = Number::model()->findByPk($id);
                $price = $number->sim_price;
                $number->sim_price = $_POST['value'];
                $number->save();
                NumberHistory::addHistoryNumber($number->id,'Цена изменена с "'.$price.'" на "'.$_POST['value'].'"');
            } catch (CDbException $e) {
                $this->ajaxError(Yii::t("app", "Error"));
            }
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    public function actionSaveNumberPrice($id) {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            try {
                $number = Number::model()->findByPk($id);
                $price = $number->number_price;
                $number->number_price = $_POST['value'];
                $number->save();
                NumberHistory::addHistoryNumber($number->id,'Цена изменена с "'.$price.'" на "'.$_POST['value'].'"');
            } catch (CDbException $e) {
                $this->ajaxError(Yii::t("app", "Error"));
            }
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    public function actionSaveShortNumber($id) {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            try {
                $number = Number::model()->findByPk($id);
                $shortNumber = $number->short_number;
                $number->short_number = $_POST['value'];
                $number->save();
                NumberHistory::addHistoryNumber($number->id,'Короткий номер сменен с "'.$codeword.'" на "'.$_POST['value'].'"');
            } catch (CDbException $e) {
                $this->ajaxError(Yii::t("app", "Error"));
            }
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    public function actionSetServicePassword($id) {
        try {
            $number = Number::model()->findByPk($id);
            $service_password = $number->service_password;
            $number->service_password = rand(100000,999999);
            $number->save();

            $message = 'Прошу на номер "'.$number->number.'" присвоить сервис гид "'.$number->service_password.'"';
            $id = Ticket::addMessage($number->id,$message);
            $ticket = Ticket::model()->findByPk($id);
            $ticket->status = Ticket::STATUS_IN_WORK_MEGAFON;
            $ticket->internal=$ticket->text;
            $ticket->sendMegafonNotification();
            $ticket->save();

            if ($service_password)
                NumberHistory::addHistoryNumber($number->id,'Пароль сервис гид сменен с "'.$service_password.'" на "'.$number->service_password.'"');
            else
                NumberHistory::addHistoryNumber($number->id,'Установлен новый пароль сервис гид: "'.$number->service_password.'"');

        } catch (CDbException $e) {
            $this->ajaxError(Yii::t("app", "Error"));
        }
        $this->redirect(Yii::app()->request->urlReferrer);
    }

    public function actionSaveServicePassword($id) {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            try {
                $number = Number::model()->findByPk($id);
                $service_password = $number->service_password;
                $number->service_password = $_POST['value'];
                $number->save();

                if ($service_password)
                    NumberHistory::addHistoryNumber($number->id,'Пароль сервис гид сменен с "'.$service_password.'" на "'.$_POST['value'].'"');
                else
                    NumberHistory::addHistoryNumber($number->id,'Установлен новый пароль сервис гид: "'.$_POST['value'].'"');

            } catch (CDbException $e) {
                $this->ajaxError(Yii::t("app", "Error"));
            }
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    public function actionSaveSupportGettingPassportVariant($id) {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            try {
                $number = Number::model()->findByPk($id);
                $object = $number->support_getting_passport_variant;
                $number->support_getting_passport_variant = $_POST['value'];
                $number->save();
                NumberHistory::addHistoryNumber($number->id,'Вариант получения паспорта сменен с "'.$object.'" на "'.$_POST['value'].'"');
            } catch (CDbException $e) {
                $this->ajaxError(Yii::t("app", "Error"));
            }
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    public function actionSaveSupportNumberRegionUsage($id) {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            try {
                $number = Number::model()->findByPk($id);
                $object = $number->support_number_region_usage;
                $number->support_number_region_usage = $_POST['value'];
                $number->save();
                NumberHistory::addHistoryNumber($number->id,'Регион использования номера сменен с "'.$object.'" на "'.$_POST['value'].'"');
            } catch (CDbException $e) {
                $this->ajaxError(Yii::t("app", "Error"));
            }
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    public function actionSaveICC($id) {
        if (Yii::app()->getRequest()->getIsPostRequest()) {

                $trx=Yii::app()->db->beginTransaction();

                $number = Number::model()->findByPk($id);

                $sim=Sim::model()->findByPk($number->sim_id);

                $blankSim=BlankSim::model()->findByAttributes(array('icc'=>$_POST['value']));
                if (!$blankSim) {
                    $this->ajaxError('Пустышки с указанным icc нет в базе');
                }
                if ($blankSim->used_dt) {
                    $this->ajaxError('Пустышка с указанным icc уже использована для восстановления');
                }
                if ($blankSim->operator_id!=$sim->operator_id) {
                    $this->ajaxError('Пустышка с указанным icc относится к другому оператору');
                }
                if ($blankSim->operator_region_id!=$sim->operator_region_id) {
                    $this->ajaxError('Пустышка с указанным icc относится к другому региону');
                }

                $blankSim->used_dt=new EDateTime();
                $blankSim->used_support_operator_id=loggedSupportOperatorId();
                $blankSim->used_number_id=$number->id;
                $blankSim->save();


                $criteria = new CDbCriteria();
                $criteria->addCondition('parent_id=:sim_id');
                $criteria->params = array(
                    ':sim_id' => $number->sim_id
                );

                Sim::model()->updateAll(array('icc' => $_POST['value']), $criteria);

                $message = "Заменить у номера ".$number->number." ICC на ".$_POST['value'];
                Ticket::addMessage($number->id,$message);

                NumberHistory::addHistoryNumber($number->id,'Установлен новый ICC: "'.$_POST['value'].'"');

                $trx->commit();
            try {

            } catch (CDbException $e) {
                $this->ajaxError(Yii::t("app", "Error"));
            }
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    private function freeNumber($id, $icc=null) {

        $number=$this->loadModel($id,'Number');

        $sim=Sim::model()->findByAttributes(array(
            'parent_id'=>$number->sim_id,
            'agent_id'=>null
        ));

        if (!$sim) {
            Yii::app()->user->setFlash('error','ошибка '.__LINE__);
            $this->refresh();
        }

        $parent_id=$sim->parent_id;

        // create new sim with same data, binded to base
        $sim->unsetAttributes(array('id','parent_act_id','agent_id','parent_id'));
        $sim->parent_agent_id=adminAgentId();
        $sim->isNewRecord=true;
        if ($icc) $sim->icc="999";
        $sim->save();
        $sim->parent_id=$sim->id;
        $sim->save();


        $number->sim_id=$sim->id;
        $number->status=Number::STATUS_FREE;
        $number->save();

        Sim::model()->updateAll(array('is_active'=>0),'parent_id=:parent_id',array(':parent_id'=>$parent_id));

        // delete subscription agreements
        $sas=SubscriptionAgreement::model()->findAllByAttributes(array('number_id'=>$number->id));
        foreach($sas as $sa) {
            SubscriptionAgreementFile::model()->deleteAllByAttributes(array('subscription_agreement_id'=>$sa->id));
            $sa->delete();
        }

        NumberHistory::addHistoryNumber($number->id,'Номер освобожден');

        if ($icc) Yii::app()->user->setFlash('success','Сим отложена');
        else Yii::app()->user->setFlash('success','Номер освобожден');


    }

    public function actionFree($id, $icc=false) {
        $trx=Yii::app()->db->beginTransaction();
            $this->freeNumber($id, $icc);
        $trx->commit();

        $this->redirect(Yii::app()->request->urlReferrer);
    }

    public function actionSetNumberRegion() {
        if (isset($_REQUEST['setNumberRegion'])) {
            $content = file_get_contents('http://rossvyaz.ru/docs/articles/DEF-9x.html');
            $content = iconv('windows-1251', 'utf-8', $content);

            preg_match_all('%<tr[^>]*>.*?</tr>%i',$content,$m);

            Yii::app()->db->createCommand("TRUNCATE TABLE tmp_number_region")->execute();

            $regions = OperatorRegion::model()->findAll();

            $balanceReportNumberBulkInsert = new BulkInsert('tmp_number_region', array('start', 'end', 'operator_id', 'region_id', 'region'));
            foreach($m[0] as $v) {
                preg_match_all('%<td[^>]*>.*?</td>%i',$v,$mm);
                $operator = mb_strtolower(trim(strip_tags(html_entity_decode($mm[0][4]))),'UTF-8');
                if ($operator=='мегафон' || $operator=='билайн') {
                    if ($operator=='мегафон') $operator_id = Operator::OPERATOR_MEGAFON_ID;
                    else $operator_id = Operator::OPERATOR_BEELINE_ID;

                    $code = trim(strip_tags(html_entity_decode($mm[0][0])));
                    $first = trim(strip_tags(html_entity_decode($mm[0][1])));
                    $last = trim(strip_tags(html_entity_decode($mm[0][2])));
                    $region = trim(strip_tags(html_entity_decode($mm[0][5])));

                    $region_id = 0;
                    foreach ($regions as $rv) {
                        if ($rv->title==$region && $rv->operator_id==$operator_id) $region_id=$rv->id;
                    }

                    $balanceReportNumberBulkInsert->insert(array(
                        'start' => $code.$first,
                        'end' => $code.$last,
                        'operator_id' => $operator_id,
                        'region_id' => $region_id,
                        'region' => $region
                    ));
                }
            }
            $balanceReportNumberBulkInsert->finish();

            $incorrectRegion = Yii::app()->db->createCommand("SELECT DISTINCT region, operator_id FROM tmp_number_region WHERE region_id=0")->queryAll();
            if (count($incorrectRegion)>0) {
                $operator = '';
                $strRegions = '<ul>';
                foreach ($incorrectRegion as $value) {
                    if ($value['operator_id']==Operator::OPERATOR_MEGAFON_ID) $operator = "Мегафон";
                    elseif ($value->$operator_id==Operator::OPERATOR_BEELINE_ID) $operator = "Билайн";
                    $strRegions .= '<li>'.$operator.' - "'.$value['region'].'"</li>';
                }
                $strRegions .= '</ul>';
                Yii::app()->user->setFlash('error', '<strong>Ошибка: </strong> Отсутствуют следующие регионы: <br/>'.$strRegions);
            } else {
                $simList = Yii::app()->db->createCommand("
                    SELECT s.number, s.operator_region_id old_region_id, t.region_id new_region_id
                    FROM
                        (SELECT DISTINCT number, operator_id, operator_region_id  FROM sim) s
                        JOIN tmp_number_region t ON (s.number>=t.start AND s.number<=t.end)
                        WHERE t.operator_id!=s.operator_id OR s.operator_region_id!=t.region_id OR s.operator_region_id is NULL
                ")->queryAll();

                $trx=Yii::app()->db->beginTransaction();

                $command = Yii::app()->db->createCommand("UPDATE sim SET operator_region_id=:operator_region_id WHERE number=:number");
                $count = 0;
                foreach ($simList as $sim) {
                    if ($sim['number'] && $sim['new_region_id']) {
                        $command->execute(array(':operator_region_id'=>$sim['new_region_id'],'number'=>$sim['number']));
                        $count++;
                    }
                }
                $trx->commit();
                Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Данные успешно добавлены. Обновлено '.$count.' записей');
            }
        }
        $this->render('numberRegion');
    }

    public function actionBulkRestore() {
        $model=new NumberBulkRestore();

        if (isset($_POST['NumberBulkRestore'])) {
            $model->setAttributes($_POST['NumberBulkRestore']);

            if ($model->validate()) {
                $msg='done';
                Yii::app()->user->setFlash('success',$msg);
                $this->refresh();
            }
        }

        $this->render('bulkRestore',array('model'=>$model));
    }

    private function setStatus($csv, $data, $status) {
        $trx = Yii::app()->db->beginTransaction();
        $wrongObjects = '';
        foreach ($csv as $v) {
            $model = $this->getModel($v, $data);
            if ($model) {
                $model->status = $status;
                $model->save();
            } else $wrongObjects .= $v[0].'; ';
        }
        $trx->commit();
        if ($wrongObjects) Yii::app()->user->setFlash('warning', '<strong>Данные объекты не найдены: </strong>'.$wrongObjects);
        Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Номера успешно освобождены.');
    }

    private function getModel($v, $data) {
        if ($data['Action']=='ICC') {
            $sim = Sim::model()->findByAttributes(array('icc'=>$v[0]),array('order'=>'id DESC'));
            $model = Number::model()->findByAttributes(array('sim_id'=>$sim->parent_id));
        } else $model = Number::model()->findByAttributes(array('number'=>$v[0]));
        return $model;
    }

    private function setCsvAndNumber(&$dataCsv, &$number, $data) {
        if ($data['Action']=='ICC') {

            foreach ($dataCsv as $n=>$value) {
                $icc .= ','.Yii::app()->db->quoteValue($n);
            }
            $sql ='select number.number, sim.icc from sim join number on (number.sim_id=sim.parent_id) where sim.id=sim.parent_id and sim.is_active=1 and sim.icc in ('.substr($icc,1).')';
            $icc = Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($icc as $value) {
                $number .= ','.Yii::app()->db->quoteValue($value['number']);
                $tmp[$value['number']] = $dataCsv[$value['icc']];
            }
            $dataCsv = $tmp;
        } else {
            foreach ($dataCsv as $n=>$value) {
                $number .= ','.Yii::app()->db->quoteValue($n);
            }
        }
    }

    private function massFree($csv, $data, $icc=null) {
        $trx = Yii::app()->db->beginTransaction();

        $number = '';
        $dataCsv = $csv;

        $this->setCsvAndNumber($dataCsv, $number, $data);
        $sql = 'select id,sim_id,number from number where number in ('.substr($number,1).')';
        $numbers = Yii::app()->db->createCommand($sql)->queryAll();
        foreach ($numbers as $value)
            $this->freeNumber($value['id'], $icc);

        $trx->commit();
        if ($wrongObjects) Yii::app()->user->setFlash('warning', '<strong>Данные объекты не найдены: </strong>'.$wrongObjects);
        Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Номера успешно освобождены.');
    }

    private static function readCSV($filename) {
        $csv=array();
        if (($handle = fopen($filename, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 10000, ";")) !== FALSE) {
                $csv[]=$data;
            }
            fclose($handle);
        }
        return $csv;
    }

    public function actionMassChange() {
        if (isset($_FILES['fileField']['tmp_name'])) {
            $csv=$this->readCSV($_FILES['fileField']['tmp_name']);
            $csv[0][0] = preg_replace('~\D+~','',$csv[0][0]);
            if (strlen($csv[0][0])>11) $head = 'ICC'; else $head = 'Number';

            $this->render('massChange',array('file'=>$csv,'head'=>$head));
            exit;
        }

        if (isset($_POST['MassChange'])) {
            $data = $_POST['MassChange'];

            if (!isset($_POST['Csv'])) {
                Yii::app()->user->setFlash('error', '<strong>Ошибка</strong> Не загружен файл.');
                $this->refresh();
            }
            $csv = unserialize($_POST['Csv']);

                if ($_POST['massFree']) {
                    $this->massFree($csv,$data);
                    $this->redirect(Yii::app()->request->urlReferrer);
                }
                if ($_POST['massFreeWait']) {
                    $this->massFree($csv,$data,1);
                    $this->redirect(Yii::app()->request->urlReferrer);
                }
                if ($_POST['massStatusUnknown']) {
                    $this->setStatus($csv,$data,Number::STATUS_UNKNOWN);
                    $this->redirect(Yii::app()->request->urlReferrer);
                }

            $message = '';
            switch ($data['type']) {
                case 'setPass':
                    $trx = Yii::app()->db->beginTransaction();
                    $numberUpdate = new BulkUpdate('number',array('id','service_password'));

                    $number = '';
                    $dataCsv = $csv;

                    $this->setCsvAndNumber($dataCsv, $number, $data);

                    $sql = 'select id,sim_id,number from number where number in ('.substr($number,1).')';
                    $numbers = Yii::app()->db->createCommand($sql)->queryAll();
                    foreach ($numbers as $value)
                        $numberUpdate->add(array('id'=>$value['id'],'service_password'=>$dataCsv[$value['number']]));
                    $numberUpdate->finish();

                    $trx->commit();

                    Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Данные успешно изменены.');
                    $this->refresh();

                    break;
                case 'tariffPlan':
                    $trx = Yii::app()->db->beginTransaction();

                    $number = '';
                    $dataCsv = $csv;

                    $this->setCsvAndNumber($dataCsv, $number, $data);

                    $sql ='select sim.id, sim.number, number.sim_id from sim join number on (number.sim_id=sim.parent_id) where sim.is_active=1 and sim.number in ('.substr($number,1).')';
                    $sims = Yii::app()->db->createCommand($sql)->queryAll();
                    foreach ($sims as $value) {
                        $tariffs = Tariff::model()->findByAttributes(array('title'=>$dataCsv[$value['number']]));
                        if ($tariffs) {
                            $criteria = new CDbCriteria();
                            $criteria->addColumnCondition(array('parent_id'=>$value['sim_id']));
                            Sim::model()->updateAll(array('tariff_id' => $tariffs->id, 'operator_id'=>$tariffs->operator_id), $criteria);
                        }
                    }
                    $trx->commit();

                    Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Данные успешно изменены.');
                    $this->refresh();
                    break;
                case 'setPA':
                    $trx = Yii::app()->db->beginTransaction();
                    $numberUpdate = new BulkUpdate('number',array('id','personal_account'));
                    $simUpdate = new BulkUpdate('sim',array('id','personal_account'));

                    $number = '';
                    $dataCsv = $csv;

                    $this->setCsvAndNumber($dataCsv, $number, $data);

                    $sql = 'select id,sim_id,number from number where number in ('.substr($number,1).')';
                    $numbers = Yii::app()->db->createCommand($sql)->queryAll();
                    foreach ($numbers as $value) {
                        //$message.= "Прошу для номера ".($data['Action']=='ICC'?'с ICC =':'')." ".$value['number']." назначить личный счёт ".$dataCsv[$value['number']]."<br/>";
                        $numberUpdate->add(array('id'=>$value['id'],'personal_account'=>$dataCsv[$value['number']]));
                    }
                    $numberUpdate->finish();


                    $sql ='select sim.id, sim.number from sim join number on (number.sim_id=sim.parent_id) where sim.is_active=1 and sim.number in ('.substr($number,1).')';
                    $sims = Yii::app()->db->createCommand($sql)->queryAll();
                    foreach ($sims as $value) {
                        $simUpdate->add(array('id'=>$value['id'],'personal_account'=>$dataCsv[$value['number']]));
                    }
                    $simUpdate->finish();
                    $trx->commit();

                    //if ($wrongObjects) Yii::app()->user->setFlash('warning', '<strong>Данные объекты не найдены: </strong>'.$wrongObjects);
                    Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Данные успешно изменены.');
                    $this->refresh();
                    break;
                case 'numberPrice':
                    $trx = Yii::app()->db->beginTransaction();
                    $numberUpdate = new BulkUpdate('number',array('id','number_price'));

                    $number = '';
                    $dataCsv = $csv;

                    $this->setCsvAndNumber($dataCsv, $number, $data);

                    $sql = 'select id,sim_id,number from number where number in ('.substr($number,1).')';
                    $numbers = Yii::app()->db->createCommand($sql)->queryAll();
                    foreach ($numbers as $value)
                        $numberUpdate->add(array('id'=>$value['id'],'number_price'=>$dataCsv[$value['number']]));
                    $numberUpdate->finish();

                    $trx->commit();

                    //if ($wrongObjects) Yii::app()->user->setFlash('warning', '<strong>Данные объекты не найдены: </strong>'.$wrongObjects);
                    Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Данные успешно изменены.');
                    $this->refresh();

                    break;
                case 'shortNumber':
                    $trx = Yii::app()->db->beginTransaction();
                    $numberUpdate = new BulkUpdate('number',array('id','short_number'));

                    $number = '';
                    $dataCsv = $csv;

                    $this->setCsvAndNumber($dataCsv, $number, $data);

                    $sql = 'select id,sim_id,number from number where number in ('.substr($number,1).')';
                    $numbers = Yii::app()->db->createCommand($sql)->queryAll();
                    foreach ($numbers as $value)
                        $numberUpdate->add(array('id'=>$value['id'],'short_number'=>$dataCsv[$value['number']]));
                    $numberUpdate->finish();

                    $trx->commit();

                    Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Данные успешно изменены.');
                    $this->refresh();

                    break;
                case 'replaceICC':
                    $trx = Yii::app()->db->beginTransaction();

                    $number = '';
                    $dataCsv = $csv;

                    $this->setCsvAndNumber($dataCsv, $number, $data);

                    $sql ='select sim.id, sim.number, number.sim_id from sim join number on (number.sim_id=sim.parent_id) where sim.is_active=1 and sim.number in ('.substr($number,1).')';
                    $sims = Yii::app()->db->createCommand($sql)->queryAll();

                    foreach ($sims as $key=>$value) {
                        $criteria = new CDbCriteria();
                        $criteria->addColumnCondition(array('number'=>$value['number']));
                        Sim::model()->updateAll(array('icc'=>$dataCsv[$value['number']]), $criteria);
                    }
                    $trx->commit();

                    Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Данные успешно изменены.');
                    $this->refresh();
                    break;
                case 'balanceStatus':
                    $trx = Yii::app()->db->beginTransaction();
                    $numberUpdate = new BulkUpdate('number',array('id','balance_status','balance_status_changed_dt'));

                    $number = '';
                    $dataCsv = $csv;

                    $this->setCsvAndNumber($dataCsv, $number, $data);

                    $sql = 'select id,sim_id,number from number where number in ('.substr($number,1).')';
                    $numbers = Yii::app()->db->createCommand($sql)->queryAll();
                    $currentDateTime=new EDateTime();
                    $dt=$currentDateTime->toMysqlDateTime();

                    $balance_status_map=array_flip(Number::getBalanceStatusLabels());

                    foreach ($numbers as $value) {
                        $text_status=$dataCsv[$value['number']];
                        $status=$balance_status_map[$text_status];
                        if (!isset($status)) {
                            Yii::app()->user->setFlash('error', "Статус баланса '$text_status' системе не известен");
                            $trx->rollback();
                            $this->refresh();
                        }
                        $numberUpdate->add(array('id'=>$value['id'],'balance_status_changed_dt'=>$dt,'balance_status'=>$status));
                    }
                    $numberUpdate->finish();

                    $trx->commit();

                    Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Данные успешно изменены.');
                    $this->refresh();

                    break;
                    break;

                default:
                    # code...
                    break;
            }
        }

        $this->render('massChange');
    }

    public function actionAgentChangeIcc($sim_id) {
        $sim = Sim::model()->findByPk($sim_id);
        $number = Number::model()->findByAttributes(array('sim_id'=>$sim->parent_id));
        $message = 'Восстановление '.$number->number.' на icc '.$sim->icc;
        Ticket::addMessage($number->id,$message,Ticket::QUEUE_AGENTS_RESTORE);
        Yii::app()->user->setFlash('success', '<strong>Операция прошла успешно</strong> Ваш запрос успешно отправлен.');
        $this->redirect(Yii::app()->request->urlReferrer);
    }
}


class Brn {

}