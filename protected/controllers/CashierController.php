<?php

class CashierController extends BaseGxController
{
    public function additionalAccessRules() {
        return array(
            array('disallow', 'roles' => array('admin')),
            array('allow', 'roles' => array('cashier')),
        );
    }

    private static function getNumberListDataProvider($criteria) {
        $model = new CashierNumberSearch();
        $model->unsetAttributes();

        if (isset($_REQUEST['CashierNumberSearch']))
            $model->setAttributes($_REQUEST['CashierNumberSearch']);

        $criteria->compare('n.number',$model->number,true);
        $criteria->compare('s.tariff_id',$model->tariff_id);
        $criteria->compare('s.operator_region_id',$model->operator_region_id);

        $sql = "from sim s
            join sim s2 on (s2.parent_id=s.id and s2.agent_id is null)
            join agent a on (a.id=s2.parent_agent_id)
            join number n on (s.parent_id=n.sim_id)
            left outer join tariff t on (t.id=s.tariff_id)
            left outer join operator_region opr on (opr.id=s.operator_region_id)
            where " . $criteria->condition;

        $totalItemCount = Yii::app()->db->createCommand('select count(*) ' . $sql)->queryScalar($criteria->params);

        $dataProvider = new CSqlDataProvider("select n.id,n.number,s.tariff_id,t.title as tariff,s.operator_region_id,
            opr.title as operator_region, n.balance, n.balance_changed_dt,a.name as agent_name, a.surname as agent_surname $sql", array(
            'totalItemCount' => $totalItemCount,
            'params' => $criteria->params,
            'sort' => array(
                'attributes' => array(
                    'number','tariff_id','operator_region_id','balance','balance_changed_dt','operator_region'
                ),
            ),
            'pagination' => array('pageSize' => Number::ITEMS_PER_PAGE)
        ));
        $list['model'] = $model;
        $list['dataProvider'] = $dataProvider;
        return $list;
    }

    private function sellBaseCriteria() {
        $criteria=new CDbCriteria();

        // look only sim records, that belongs to base
        $criteria->addCondition('s.id=s.parent_id');
        $criteria->compare('s.operator_id',Operator::OPERATOR_MEGAFON_ID);
        //$criteria->compare('s.icc','999');
        $criteria->compare('n.status',Number::STATUS_FREE);
        $criteria->addInCondition('n.balance_status',array(Number::BALANCE_STATUS_NOT_CHANGING,Number::BALANCE_STATUS_NOT_CHANGING_PLUS));

        // sim must be not passed to any agent
        $criteria->compare('s2.parent_agent_id',adminAgentId());

        return $criteria;
    }

    public function actionSellList() {

        $list = $this->getNumberListDataProvider($this->sellBaseCriteria());

        $this->render('sellList', array(
            'model' => $list['model'],
            'dataProvider' => $list['dataProvider']
        ));
    }

    public function actionSell($id) {
        $criteria=$this->sellBaseCriteria();
        $criteria->compare('n.id',$id);

        $numbersCount=Yii::app()->db->createCommand("select count(*) from sim s
            join sim s2 on (s2.parent_id=s.id and s2.agent_id is null)
            join number n on (s.parent_id=n.sim_id) where {$criteria->condition}")->queryScalar($criteria->params);

        if ($numbersCount!=1) {
            Yii::app()->user->setFlash('error','Продажа данного номера невозможна');
            $this->redirect(array('sellList'));
        }

        $number=Number::model()->findByPk($id);

        $model=new CashierSellForm();

        if (isset($_POST['CashierSellForm'])) {
            $model->setAttributes($_POST['CashierSellForm']);

            if ($model->type==CashierSellForm::TYPE_AGENT && $model->payment==CashierSellForm::PAYMENT_CASH) $model->setScenario('agent_id');
            if ($model->type==CashierSellForm::TYPE_AGENT && $model->payment==CashierSellForm::PAYMENT_NOT_CASH) $model->setScenario('agent_id_comment');
            if ($model->type==CashierSellForm::TYPE_CLIENT && $model->payment==CashierSellForm::PAYMENT_NOT_CASH) $model->setScenario('comment');

            if ($model->validate()) {
                $trx=Yii::app()->db->beginTransaction();

                $sim=Sim::model()->findByPk($number->sim_id);

                // add acts for agent
                if ($model->type==CashierSellForm::TYPE_AGENT) {
                    $recursiveInfo=array();
                    $destAgent=Agent::model()->findByPk($model->agent_id);

                    $agent=$destAgent;
                    while($agent) {
                        if ($agent->id==adminAgentId()) break;
                        $act=new Act;
                        $act->agent_id=$agent->id;
                        $act->type=Act::TYPE_SIM;
                        $act->sum=0;
                        $act->comment='Продажа номера "'.$number->number.'" кассиром агенту "'.$destAgent.'"';
                        $act->dt=new EDateTime();
                        $act->save();
                        $recursiveInfo[]=array('agent'=>$agent,'act'=>$act);
                        $agent=$agent->parent;
                    }
                    $recursiveInfo=array_reverse($recursiveInfo);
                    $recursiveInfo[]=array('agent'=>new Agent,'act'=>new Act);

                    $parentAgentId=adminAgentId();
                    $parentActId=null;
                    foreach($recursiveInfo as $rInfo) {
                        if ($parentAgentId!=adminAgentId()) {
                            $sim->isNewRecord=true;
                            $sim->id=null;

                            $sim->parent_agent_id=$parentAgentId;
                            $sim->parent_act_id=$parentActId;

                        }
                        $sim->agent_id=$rInfo['agent']->id;
                        $sim->act_id=$rInfo['act']->id;

                        $sim->save();

                        $parentAgentId=$rInfo['agent']->id;
                        $parentActId=$rInfo['act']->id;
                    }

                    foreach($recursiveInfo as $rInfo)
                        if (!$rInfo['act']->isNewRecord) {
                            $rInfo['act']->updateSimCount();
                            $rInfo['act']->save();
                        }
                }

                $cashierSellNumber=new CashierSellNumber;
                $cashierSellNumber->dt=new EDateTime;
                $cashierSellNumber->support_operator_id=loggedSupportOperatorId();
                $cashierSellNumber->number_id=$number->id;
                $cashierSellNumber->sum=$model->sum;
                $cashierSellNumber->type=$model->type;

                if ($model->payment==CashierSellForm::PAYMENT_CASH) {
                    $cashierDebitCredit=new CashierDebitCredit;
                    $cashierDebitCredit->dt=$cashierSellNumber->dt;
                    $cashierDebitCredit->support_operator_id=loggedSupportOperatorId();
                    $cashierDebitCredit->comment='Продажа номера '.$number->number;
                    $cashierDebitCredit->sum=$model->sum;
                    $cashierDebitCredit->type=CashierDebitCredit::TYPE_NUMBER_SELL;
                    $cashierDebitCredit->save();

                    $cashierSellNumber->cashier_debit_credit_id=$cashierDebitCredit->id;
                }

                $cashierSellNumber->comment=$model->comment;
                $cashierSellNumber->save();

                $number->status=Number::STATUS_SOLD;
                $number->save();

                $trx->commit();

                Yii::app()->user->setFlash('success','Номер успешно продан');
                $this->redirect(array('cashier/sellList'));
            }
        }

        $model->setScenario('agent_id_comment');

        $this->render('sell', array(
            'number' => $number,
            'model' => $model,
        ));
    }

    private function restoreBaseCriteria() {
        $criteria=new CDbCriteria();

        // look only sim records, that belongs to base
        $criteria->addCondition('s.id=s.parent_id');
        $criteria->compare('s.operator_id',Operator::OPERATOR_MEGAFON_ID);
        $criteria->addNotInCondition('n.status',array(Number::STATUS_FREE));

        return $criteria;
    }

    public function actionServiceList() {

        $list = $this->getNumberListDataProvider($this->restoreBaseCriteria());

        $this->render('serviceList', array(
            'model' => $list['model'],
            'dataProvider' => $list['dataProvider']
        ));
    }

    private function getBalancesDataProvider($number) {
        $sql="from balance_report_number brn
              join balance_report br on (br.id=brn.balance_report_id)
              where brn.number_id=:number_id";
        $sqlParams=array(':number_id'=>$number->id);
        $totalItemCount=Yii::app()->db->createCommand("select count(*) $sql")->queryScalar($sqlParams);

        $balancesDataProvider = new CSqlDataProvider("select br.dt,brn.balance $sql order by dt desc", array(
            'totalItemCount' => $totalItemCount,
            'params' => $sqlParams,
            'pagination' => array('pageSize' => 10)
        ));

        return $balancesDataProvider;
    }

    public function actionRestoreFinish($id) {
        $megafonAppRestoreNumber=MegafonAppRestoreNumber::model()->findByAttributes(array('number_id'=>$id,'status'=>MegafonAppRestoreNumber::STATUS_DONE,'sim_given'=>false));
        if (!$megafonAppRestoreNumber) $this->redirect(array('serviceList'));

        $number=Number::model()->findByPk($id);
        $sim=Sim::model()->findByPk($number->sim_id);

        $model=new CashierNumberRestoreFinishForm;

        if (Yii::app()->request->isPostRequest) {
            $model->setAttributes($_POST['CashierNumberRestoreFinishForm']);
            if ($megafonAppRestoreNumber->cashier_debit_credit_id || $model->validate()) {

                $trx=Yii::app()->db->beginTransaction();

                if (!$megafonAppRestoreNumber->cashier_debit_credit_id) {
                    $cashierDebitCredit=new CashierDebitCredit;
                    $cashierDebitCredit->support_operator_id=loggedSupportOperatorId();
                    $cashierDebitCredit->dt=new EDateTime();
                    $cashierDebitCredit->sum=$model->sum;
                    $cashierDebitCredit->comment='Восстановление номера '.$number->number;
                    $cashierDebitCredit->type=CashierDebitCredit::TYPE_NUMBER_RESTORE;
                    $cashierDebitCredit->save();
                    $megafonAppRestoreNumber->cashier_debit_credit_id=$cashierDebitCredit->id;
                }

                $megafonAppRestoreNumber->sim_given=true;
                $megafonAppRestoreNumber->save();

                $number->status=Number::STATUS_ACTIVE;
                $number->save();

                $trx->commit();

                Yii::app()->user->setFlash('success','Восстановление номера успешно завершено');
                $this->redirect(array('serviceList'));
            }
        }
        $this->render('restoreFinish', array(
            'number' => $number,
            'sim' => $sim,
            'balancesDataProvider'=>$this->getBalancesDataProvider($number),
            'megafonAppRestoreNumber'=>$megafonAppRestoreNumber,
            'model'=>$model
        ));
    }

    public function actionRestoreDoCancel($id) {
        $megafonAppRestoreNumber=MegafonAppRestoreNumber::model()->findByAttributes(array('number_id'=>$id,'status'=>MegafonAppRestoreNumber::STATUS_PROCESSING));
        if (!$megafonAppRestoreNumber) $this->redirect(array('serviceList'));

        $number=Number::model()->findByPk($id);

        $trx=Yii::app()->db->beginTransaction();

        $megafonAppRestoreNumber->status=MegafonAppRestoreNumber::STATUS_REJECTED;
        if ($megafonAppRestoreNumber->cashier_debit_credit_id) {
            $cashierDebitCredit=new CashierDebitCredit;
            $cashierDebitCredit->support_operator_id=loggedSupportOperatorId();
            $cashierDebitCredit->dt=new EDateTime();
            $cashierDebitCredit->sum=-$megafonAppRestoreNumber->cashierDebitCredit->sum;
            $cashierDebitCredit->comment='Возврат денег за отклоненное восстановление номера '.$number->number;
            $cashierDebitCredit->type=CashierDebitCredit::TYPE_NUMBER_RESTORE_REJECT;
            $cashierDebitCredit->save();
        }

        $megafonAppRestoreNumber->save();

        $number->status=Number::STATUS_ACTIVE;
        $number->save();

        $trx->commit();

        Yii::app()->user->setFlash('success','Восстановление номера успешно отклонено');
        $this->redirect(array('serviceList'));
    }

    public function actionRestoreCancel($id) {
        $megafonAppRestoreNumber=MegafonAppRestoreNumber::model()->findByAttributes(array('number_id'=>$id,'status'=>MegafonAppRestoreNumber::STATUS_PROCESSING));
        if (!$megafonAppRestoreNumber) $this->redirect(array('serviceList'));

        $message='Номер уже отправлен на восстановление в заявлении №'.$megafonAppRestoreNumber->megafon_app_restore_id.' от '.
            $megafonAppRestoreNumber->megafonAppRestore->dt->format('d.m.Y');

        $number=Number::model()->findByPk($id);
        $sim=Sim::model()->findByPk($number->sim_id);

        $this->render('restoreCancel', array(
            'number' => $number,
            'sim' => $sim,
            'message' => $message,
            'balancesDataProvider'=>$this->getBalancesDataProvider($number),
            'megafonAppRestoreNumber'=>$megafonAppRestoreNumber
        ));
    }

    public function actionRestore($id) {
        $megafonAppRestoreNumber=MegafonAppRestoreNumber::model()->findByAttributes(array('number_id'=>$id,'status'=>MegafonAppRestoreNumber::STATUS_DONE,'sim_given'=>false));
        if ($megafonAppRestoreNumber) $this->redirect(array('restoreFinish','id'=>$id));

        $megafonAppRestoreNumber=MegafonAppRestoreNumber::model()->findByAttributes(array('number_id'=>$id,'status'=>MegafonAppRestoreNumber::STATUS_PROCESSING));
        if ($megafonAppRestoreNumber) $this->redirect(array('restoreCancel','id'=>$id));

        $criteria=$this->restoreBaseCriteria();
        $criteria->compare('n.id',$id);

        $numbersCount=Yii::app()->db->createCommand("select count(*) from sim s
            join sim s2 on (s2.parent_id=s.id and s2.agent_id is null)
            join number n on (s.parent_id=n.sim_id) where {$criteria->condition}")->queryScalar($criteria->params);

        if ($numbersCount!=1) {
            Yii::app()->user->setFlash('error','Восстановление данного номера невозможно');
            $this->redirect(array('serviceList'));
        }

        $number=Number::model()->findByPk($id);
        $sim=Sim::model()->findByPk($number->sim_id);

        $model=new CashierNumberRestore1();
        $model->setScenario('with_sum');

        if (isset($_POST['CashierNumberRestore1'])) {
            $model->setAttributes($_POST['CashierNumberRestore1']);

            if ($model->payment!=CashierNumberRestore1::PAYMENT_IMMEDIATE)
                $model->setScenario('');

            if ($model->validate()) {
                $megafonAppRestore=MegafonAppRestore::getCurrent();

                $megafonAppRestoreNumber=new MegafonAppRestoreNumber;

                $megafonAppRestoreNumber->megafon_app_restore_id=$megafonAppRestore->id;
                $megafonAppRestoreNumber->status=MegafonAppRestoreNumber::STATUS_PROCESSING;
                $megafonAppRestoreNumber->number_id=$number->id;
                $megafonAppRestoreNumber->support_operator_id=loggedSupportOperatorId();
                $megafonAppRestoreNumber->sim_type=$model->sim_type;
                $megafonAppRestoreNumber->contact_name=$model->contact_name;
                $megafonAppRestoreNumber->contact_phone=$model->contact_phone;

                $trx=Yii::app()->db->beginTransaction();

                if ($model->payment==CashierNumberRestore1::PAYMENT_IMMEDIATE) {
                    $cashierDebitCredit=new CashierDebitCredit;
                    $cashierDebitCredit->support_operator_id=loggedSupportOperatorId();
                    $cashierDebitCredit->dt=new EDateTime();
                    $cashierDebitCredit->sum=$model->sum;
                    $cashierDebitCredit->comment='Восстановление номера '.$number->number;
                    $cashierDebitCredit->type=CashierDebitCredit::TYPE_NUMBER_RESTORE;
                    $cashierDebitCredit->save();
                    $megafonAppRestoreNumber->cashier_debit_credit_id=$cashierDebitCredit->id;
                }

                $megafonAppRestoreNumber->save();

                NumberHistory::addHistoryNumber($number->id,'Номер добавлен в заявление на восстановление №'.$megafonAppRestoreNumber->megafonAppRestore->id.' от '.$megafonAppRestoreNumber->megafonAppRestore->dt->format('d.m.Y'));

                $number->status=Number::STATUS_RESTORE;
                $number->save();

                $trx->commit();

                Yii::app()->user->setFlash('success','Номер добавлен в заявку на восстановление');
                $this->redirect(array('cashier/serviceList'));
            }
        }

        $this->render('restore', array(
            'number' => $number,
            'sim' => $sim,
            'model' => $model,
            'balancesDataProvider'=>$this->getBalancesDataProvider($number)
        ));
    }

    public function actionStats() {
        $model=new CashierStatistic();

        if (isset($_POST['CashierStatistic'])) {
            $this->redirect(array('stats',
                'CashierStatistic[date_from]'=>$_POST['CashierStatistic']['date_from'],
                'CashierStatistic[date_to]'=>$_POST['CashierStatistic']['date_to'],
                'CashierStatistic[support_operator_id]'=>$_POST['CashierStatistic']['support_operator_id'],
            ));
        }

        if (isset($_REQUEST['CashierStatistic'])) {
            $model->setAttributes($_REQUEST['CashierStatistic']);
        } else {
            $this->redirect(array('stats',
                'CashierStatistic[date_from]'=>new EDateTime("",null,'date'),
            ));
        }

        $date_from=new EDateTime($model->date_from);
        $date_to=$date_from;

        $params=array(
            ':date_from'=>$date_from->toMysqlDate(),
            ':date_to'=>$date_to->toMysqlDate(),
        );

        if (Yii::app()->user->role=='cashier') {
            $model->support_operator_id=loggedSupportOperatorId();
        }

        $where='';
        if ($model->support_operator_id) {
            $where=' and m.support_operator_id=:support_operator_id';
            $params[':support_operator_id']=$model->support_operator_id;
        }

        $sells=Yii::app()->db->createCommand("
            select count(*) from cashier_debit_credit m
            where type=:type and m.dt>=:date_from and m.dt<DATE_ADD(:date_to,INTERVAL 1 DAY) $where")->
            queryScalar(array_merge($params,array(':type'=>CashierDebitCredit::TYPE_NUMBER_SELL)));

        $restores=Yii::app()->db->createCommand("
            select count(*) from cashier_debit_credit m
            where type=:type and m.dt>=:date_from and m.dt<DATE_ADD(:date_to,INTERVAL 1 DAY) $where")->
            queryScalar(array_merge($params,array(':type'=>CashierDebitCredit::TYPE_NUMBER_RESTORE)));

        $sum=Yii::app()->db->createCommand("select sum(sum) from cashier_debit_credit m where sum>0 and dt>=:date_from and dt<DATE_ADD(:date_to,INTERVAL 1 DAY) $where")->
            queryScalar($params);

        $summary=array(array(
            'sells'=>$sells,
            'restores'=>$restores,
            'sum'=>$sum
        ));

        $cashierSellNumber=new CashierSellNumberSearch;
        if (isset($_REQUEST['CashierSellNumberSearch']))
            $cashierSellNumber->setAttributes($_REQUEST['CashierSellNumberSearch']);

        $criteria=new CDbCriteria;
        if ($model->support_operator_id) {
            $criteria->compare('csn.support_operator_id',$model->support_operator_id);
        }

        $criteria->compare('n.number',$cashierSellNumber->number,true);
        $criteria->compare('csn.type',$cashierSellNumber->type);
        $criteria->addCondition('cdc.dt>=:date_from and cdc.dt<DATE_ADD(:date_to,INTERVAL 1 DAY)');
        $criteria->compare('cdc.type',CashierDebitCredit::TYPE_NUMBER_SELL);
        $criteria->params[':date_from']=$date_from->toMysqlDate();
        $criteria->params[':date_to']=$date_to->toMysqlDate();

        $sql = "from cashier_debit_credit cdc
            join cashier_sell_number csn on (csn.cashier_debit_credit_id=cdc.id)
            join number n on (n.id=csn.number_id)
            where " . $criteria->condition;

        $totalItemCount = Yii::app()->db->createCommand('select count(*) ' . $sql)->queryScalar($criteria->params);

        $cashierNumberSellDataProvider = new CSqlDataProvider('select csn.type,n.number,csn.sum ' . $sql, array(
            'totalItemCount' => $totalItemCount,
            'params' => $criteria->params,
            'sort' => array(
                'attributes' => array(
                    'type','number','sum'
                ),
            ),
            'pagination' => array('pageSize' => Number::ITEMS_PER_PAGE)
        ));

        $cashierRestoreNumber=new CashierRestoreNumberSearch;
        if (isset($_REQUEST['CashierRestoreNumberSearch']))
            $cashierRestoreNumber->setAttributes($_REQUEST['CashierRestoreNumberSearch']);

        $criteria=new CDbCriteria;
        if ($model->support_operator_id) {
            $criteria->compare('cdc.support_operator_id',$model->support_operator_id);
        }

        $criteria->compare('n.number',$cashierRestoreNumber->number,true);
        $criteria->addCondition('cdc.dt>=:date_from and cdc.dt<DATE_ADD(:date_to,INTERVAL 1 DAY)');
        $criteria->compare('cdc.type',CashierDebitCredit::TYPE_NUMBER_RESTORE);
        $criteria->params[':date_from']=$date_from->toMysqlDate();
        $criteria->params[':date_to']=$date_to->toMysqlDate();

        $sql = "from cashier_debit_credit cdc
            join megafon_app_restore_number marn on (marn.cashier_debit_credit_id=cdc.id)
            join number n on (n.id=marn.number_id)
            where " . $criteria->condition;

        $totalItemCount = Yii::app()->db->createCommand('select count(*) ' . $sql)->queryScalar($criteria->params);

        $cashierNumberRestoreDataProvider = new CSqlDataProvider('select n.number,cdc.sum ' . $sql, array(
            'totalItemCount' => $totalItemCount,
            'params' => $criteria->params,
            'sort' => array(
                'attributes' => array(
                    'number','sum'
                ),
            ),
            'pagination' => array('pageSize' => Number::ITEMS_PER_PAGE)
        ));

        $collection=new CashierCollection;
        $collection->cashier_support_operator_id=$model->support_operator_id;
        $collectionDataProvider=$collection->search();

        $curDate=new EDateTime($date_from->toMysqlDate());
        $tomorrowDate=$curDate->modifiedCopy('+1 DAY');

        $balance=$this->getBalance($model->support_operator_id);
        $morningBalance=$this->getBalance($model->support_operator_id,$curDate);
        $eveningBalance=$this->getBalance($model->support_operator_id,$tomorrowDate);

        $this->render('stats',array(
            'summary'=>new CArrayDataProvider($summary),
            'model'=>$model,
            'cashierNumberSellDataProvider'=>$cashierNumberSellDataProvider,
            'cashierNumberRestoreDataProvider'=>$cashierNumberRestoreDataProvider,
            'cashierRestoreNumberModel'=>$cashierRestoreNumber,
            'cashierSellNumberModel'=>$cashierSellNumber,
            //'total'=>$total,
            'balance'=>$balance,
            'collectionDataProvider'=>$collectionDataProvider,
            'morningBalance'=>$morningBalance,
            'eveningBalance'=>$eveningBalance,
        ));
    }

    private function getBalance($support_operator_id,$dt=null) {
        if (!$dt) $dt=new EDateTime();

        if ($support_operator_id) {
            $balance=Yii::app()->db->createCommand("select sum(`sum`) from cashier_debit_credit where support_operator_id=:support_operator_id and dt<:dt")->queryScalar(array(
                ':support_operator_id'=>$support_operator_id,
                ':dt'=>$dt->toMysqlDateTime()
            ));
        } else {
            $balance=Yii::app()->db->createCommand("select sum(`sum`) from cashier_debit_credit where dt<:dt")->queryScalar(array(
                ':dt'=>$dt->toMysqlDateTime()
            ));
        }

        return number_format($balance,2);
    }

    public function actionCollectionStep1($cashier_support_operator_id) {
        $collection=new CashierCollection();
        $collection->dt=new EDateTime;
        $collection->cashier_support_operator_id=$cashier_support_operator_id;
        $balance=$this->getBalance($collection->cashier_support_operator_id);

        if (isset($_POST['CashierCollection'])) {
            $collection->setAttributes($_POST['CashierCollection']);

            $collection->validate();

            if (!$collection->hasErrors() && $collection->sum>$balance+1e-6)
                $collection->addError('sum','Сумма инкассации не должна превышать баланс кассы');
            if (!$collection->hasErrors()) {
                $sd=new SessionData('collection');
                $data=array('collection'=>$collection->attributes,'code'=>rand(100000,999999));
                $key=$sd->add($data);

                $msg="Код подтверждения инкассации {$data['code']} (сумма {$collection->sum}, кассир {$collection->cashierSupportOperator})";
                Sms::send($collection->collectorSupportOperator->phone,$msg);

                $this->redirect(array('collectionStep2','key'=>$key));
            }
        }
        $this->render('collection_step1',array(
            'collection'=>$collection,
            'cashier'=>SupportOperator::model()->findByPk($collection->cashier_support_operator_id),
            'balance'=>$balance
        ));
    }

    public function actionCollectionStep2($key) {
        $sd=new SessionData('collection');
        $data=$sd->get($key);
        $collection=new CashierCollection();
        $collection->attributes=$data['collection'];
        if (!$data) $this->redirect(array('stats'));

        if (isset($_REQUEST['code'])) {
            if ($_REQUEST['code']==$data['code']) {

                $cashierDebitCredit=new CashierDebitCredit;
                $cashierDebitCredit->dt=$collection->dt;
                $cashierDebitCredit->support_operator_id=$collection->cashier_support_operator_id;
                $cashierDebitCredit->comment='Инкассация '.$collection->collectorSupportOperator;
                $cashierDebitCredit->sum=-$collection->sum;
                $cashierDebitCredit->type=CashierDebitCredit::TYPE_COLLECTION;
                $cashierDebitCredit->save();

                $collection->cashier_debit_credit_id=$cashierDebitCredit->id;
                $collection->save();
                $sd->delete($key);

                $this->redirect(array('stats'));
            } else {
                Yii::app()->user->setFlash('error','Код подтверждения неверен');
                $this->refresh();
            }
        }

        $this->render('collection_step2',array(
            'cashier'=>SupportOperator::model()->findByPk($collection->cashier_support_operator_id),
            'balance'=>$this->getBalance($collection->cashier_support_operator_id),
            'collection'=>$collection
        ));
    }
}