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
        $criteria->compare('s.icc','999');
        $criteria->compare('n.status',Number::STATUS_FREE);

        // sim must be not passed to any agent
        //$criteria->compare('s2.parent_agent_id',adminAgentId());

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

            if ($model->type==CashierSellForm::TYPE_AGENT)
                $model->setScenario('to_agent');

            $model->validate();

            $sim=Sim::model()->findByPk($number->sim_id);

            $blankSim=BlankSim::model()->findByAttributes(array('icc'=>$model->icc));
            if (!$blankSim) {
                $model->addError('icc','Пустышки с указанным icc нет в базе');
            } else {
                if ($blankSim->used_dt) {
                    $model->addError('icc','Пустышка с указанным icc уже использована для восстановления');
                }
                if ($blankSim->operator_id!=$sim->operator_id) {
                    $model->addError('icc','Пустышка с указанным icc относится к другому оператору');
                }
                if ($blankSim->operator_region_id!=$sim->operator_region_id) {
                    $model->addError('icc','Пустышка с указанным icc относится к другому региону');
                }
            }

            $errors=$model->getErrors();
            if (empty($errors)) {
                $trx=Yii::app()->db->beginTransaction();

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


                $blankSim->used_dt=new EDateTime();
                $blankSim->used_support_operator_id=loggedSupportOperatorId();
                $blankSim->used_number_id=$number->id;
                $blankSim->save();


                $criteria = new CDbCriteria();
                $criteria->addCondition('parent_id=:sim_id');
                $criteria->params = array(
                    ':sim_id' => $number->sim_id
                );

                Sim::model()->updateAll(array('icc' => $model->icc), $criteria);

                $message = "Заменить у номера ".$number->number." ICC на ".$model->icc;

                $ticketId=Ticket::addMessage($number->id,$message);

                $ticket = Ticket::model()->findByPk($ticketId);
                $ticket->status = Ticket::STATUS_IN_WORK_MEGAFON;
                $ticket->internal=$ticket->text;
                $ticket->sendMegafonNotification();
                $ticket->save();

                NumberHistory::addHistoryNumber($number->id,'Установлен новый ICC: "'.$_POST['value'].'"');

                $cashierNumber=new CashierNumber();
                $cashierNumber->dt=new EDateTime();
                $cashierNumber->support_operator_id=loggedSupportOperatorId();
                $cashierNumber->number_id=$number->id;
                $cashierNumber->type=CashierNumber::TYPE_SELL;
                $cashierNumber->ticket_id=$ticketId;
                $cashierNumber->sum=500;
                $cashierNumber->sum_cashier=0;
                $cashierNumber->save();

                $number->status=Number::STATUS_SOLD;
                $number->save();

                $trx->commit();

                Yii::app()->user->setFlash('success','Номер успешно продан');
                $this->redirect(array('cashier/sellList'));
            }
        }

        $this->render('sell', array(
            'number' => $number,
            'model' => $model,
            'prefixRegionModel'=>new IccPrefixRegion
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

    public function actionRestore($id) {
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

        $model=new CashierRestoreForm();

        if (isset($_POST['CashierRestoreForm'])) {
            $model->setAttributes($_POST['CashierRestoreForm']);

            $model->validate();

            $sim=Sim::model()->findByPk($number->sim_id);

            $blankSim=BlankSim::model()->findByAttributes(array('icc'=>$model->icc));
            if (!$blankSim) {
                $model->addError('icc','Пустышки с указанным icc нет в базе');
            } else {
                if ($blankSim->used_dt) {
                    $model->addError('icc','Пустышка с указанным icc уже использована для восстановления');
                }
                if ($blankSim->operator_id!=$sim->operator_id) {
                    $model->addError('icc','Пустышка с указанным icc относится к другому оператору');
                }
                if ($blankSim->operator_region_id!=$sim->operator_region_id) {
                    $model->addError('icc','Пустышка с указанным icc относится к другому региону');
                }
            }

            $errors=$model->getErrors();
            if (empty($errors)) {
                $trx=Yii::app()->db->beginTransaction();

                $blankSim->used_dt=new EDateTime();
                $blankSim->used_support_operator_id=loggedSupportOperatorId();
                $blankSim->used_number_id=$number->id;
                $blankSim->save();


                $criteria = new CDbCriteria();
                $criteria->addCondition('parent_id=:sim_id');
                $criteria->params = array(
                    ':sim_id' => $number->sim_id
                );

                Sim::model()->updateAll(array('icc' => $model->icc), $criteria);

                $message = "Заменить у номера ".$number->number." ICC на ".$model->icc;

                $ticketId=Ticket::addMessage($number->id,$message);

                $ticket = Ticket::model()->findByPk($ticketId);
                $ticket->status = Ticket::STATUS_IN_WORK_MEGAFON;
                $ticket->internal=$ticket->text;
                $ticket->sendMegafonNotification();
                $ticket->save();

                NumberHistory::addHistoryNumber($number->id,'Установлен новый ICC: "'.$_POST['value'].'"');

                $cashierNumber=new CashierNumber();
                $cashierNumber->dt=new EDateTime();
                $cashierNumber->support_operator_id=loggedSupportOperatorId();
                $cashierNumber->number_id=$number->id;
                $cashierNumber->type=CashierNumber::TYPE_RESTORE;
                $cashierNumber->ticket_id=$ticketId;
                $cashierNumber->sum=300;
                $cashierNumber->sum_cashier=200;
                $cashierNumber->save();

                $trx->commit();

                Yii::app()->user->setFlash('success','Номер успешно восстановлен');
                $this->redirect(array('cashier/serviceList'));
            }
        }

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

        $this->render('restore', array(
            'number' => $number,
            'model' => $model,
            'balancesDataProvider'=>$balancesDataProvider
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
                //'CashierStatistic[date_from]'=>strval(new EDateTime("-7 Day")),
                //'CashierStatistic[date_to]'=>new EDateTime("")
                'CashierStatistic[date_from]'=>new EDateTime("",null,'date'),
                //'CashierStatistic[date_to]'=>new EDateTime("",null,'date')
            ));
        }

        $date_from=new EDateTime($model->date_from);
        //$date_to=new EDateTime($model->date_to);
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
            $where=' and support_operator_id=:support_operator_id';
            $params[':support_operator_id']=$model->support_operator_id;
        }
        $params[':role']='cashier';

        $rows=Yii::app()->db->createCommand("
            select so.surname,so.name,cnt_sell,cnt_restore,sum,sum_cashier
            from support_operator so
            join
            (
                select support_operator_id,sum(if(type='SELL',1,0)) as cnt_sell,sum(if(type='RESTORE',1,0)) as cnt_restore,sum(`sum`) as `sum`,sum(sum_cashier) as sum_cashier
                from cashier_number
                where confirmed=1 and dt>=:date_from and dt<DATE_ADD(:date_to,INTERVAL 1 DAY) $where
                group by support_operator_id
            ) tmp on (so.id=tmp.support_operator_id)
            where so.role=:role
            order by surname,name
        ")->queryAll(true,$params);


        $cashierNumber=new CashierNumberSearch2;
        if (isset($_REQUEST['CashierNumberSearch2']))
            $cashierNumber->setAttributes($_REQUEST['CashierNumberSearch2']);

        $criteria=new CDbCriteria;
        if ($model->support_operator_id) {
            $criteria->compare('cn.support_operator_id',$model->support_operator_id);
        }
        $criteria->addCondition('type=:type');
        $criteria->params[':type']=CashierNumber::TYPE_SELL;

        $criteria->compare('cn.support_operator_id',$cashierNumber->support_operator_id);
        $criteria->compare('n.number',$cashierNumber->number,true);
        $criteria->addCondition('cn.dt>=:date_from and cn.dt<DATE_ADD(:date_to,INTERVAL 1 DAY)');
        $criteria->params[':date_from']=$date_from->toMysqlDate();
        $criteria->params[':date_to']=$date_to->toMysqlDate();
        $criteria->compare('cn.confirmed',$cashierNumber->confirmed);

        $sql = "from cashier_number cn
            join number n on (n.id=cn.number_id)
            left outer join support_operator so on (so.id=cn.support_operator_id)
            where " . $criteria->condition;


        $totalItemCount = Yii::app()->db->createCommand('select count(*) ' . $sql)->queryScalar($criteria->params);

        $cashierNumberSellDataProvider = new CSqlDataProvider('select cn.support_operator_id,so.name,so.surname,n.number,cn.confirmed,cn.sum,cn.sum_cashier ' . $sql, array(
            'totalItemCount' => $totalItemCount,
            'params' => $criteria->params,
            'sort' => array(
                'attributes' => array(
                    'support_operator_id','number','confirmed','sum','sum_cashier'
                ),
            ),
            'pagination' => array('pageSize' => CashierNumber::ITEMS_PER_PAGE)
        ));


        $criteria->params[':type']=CashierNumber::TYPE_RESTORE;

        $totalItemCount = Yii::app()->db->createCommand('select count(*) ' . $sql)->queryScalar($criteria->params);

        $cashierNumberRestoreDataProvider = new CSqlDataProvider('select cn.support_operator_id,so.name,so.surname,n.number,cn.confirmed,cn.sum,cn.sum_cashier ' . $sql, array(
            'totalItemCount' => $totalItemCount,
            'params' => $criteria->params,
            'sort' => array(
                'attributes' => array(
                    'support_operator_id','number','confirmed','sum','sum_cashier'
                ),
            ),
            'pagination' => array('pageSize' => CashierNumber::ITEMS_PER_PAGE)
        ));


        // total statistic
        $total=Yii::app()->db->createCommand("select sum(`sum`) from cashier_number cn where cn.dt>=:date_from and cn.dt<DATE_ADD(:date_to,INTERVAL 1 DAY)")->queryScalar(array(
            ':date_from'=>$date_from->toMysqlDate(),
            ':date_to'=>$date_to->toMysqlDate()
        ));

        //if ($model->support_operator_id) {
            $collection=new CashierCollection;
            $collection->cashier_support_operator_id=$model->support_operator_id;
            $collectionDataProvider=$collection->search();
        //}


        $curDate=new EDateTime($date_from->toMysqlDate());
        $tomorrowDate=$curDate->modifiedCopy('+1 DAY');

        $balance=$this->getBalance($model->support_operator_id);
        $morningBalance=$this->getBalance($model->support_operator_id,$curDate);
        $eveningBalance=$this->getBalance($model->support_operator_id,$tomorrowDate);

        $this->render('stats',array(
            'dataProvider'=>new CArrayDataProvider($rows),
            'model'=>$model,
            'cashierNumberSellDataProvider'=>$cashierNumberSellDataProvider,
            'cashierNumberRestoreDataProvider'=>$cashierNumberRestoreDataProvider,
            'cashierNumberModel'=>$cashierNumber,
            'total'=>$total,
            'balance'=>$balance,
            'collectionDataProvider'=>$collectionDataProvider,
            'morningBalance'=>$morningBalance,
            'eveningBalance'=>$eveningBalance,
        ));
    }

    private function getBalance($support_operator_id,$dt=null) {
        if (!$dt) $dt=new EDateTime();

        if ($support_operator_id) {
            $total_in=Yii::app()->db->createCommand("select sum(`sum`) from cashier_number where support_operator_id=:support_operator_id and dt<:dt")->queryScalar(array(
                ':support_operator_id'=>$support_operator_id,
                ':dt'=>$dt->toMysqlDateTime()
            ));
            $total_out=Yii::app()->db->createCommand("select sum(`sum`) from cashier_collection where cashier_support_operator_id=:support_operator_id and dt<:dt")->queryScalar(array(
                ':support_operator_id'=>$support_operator_id,
                ':dt'=>$dt->toMysqlDateTime()
            ));
        } else {
            $total_in=Yii::app()->db->createCommand("select sum(`sum`) from cashier_number where dt<:dt")->queryScalar(array(
                ':dt'=>$dt->toMysqlDateTime()
            ));
            $total_out=Yii::app()->db->createCommand("select sum(`sum`) from cashier_collection where dt<:dt")->queryScalar(array(
                ':dt'=>$dt->toMysqlDateTime()
            ));
        }

        $balance=$total_in-$total_out;

        return $balance;
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