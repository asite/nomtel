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
            opr.title as operator_region, (select balance from balance_report_number where number_id=n.id order by
            id desc limit 0,1) as last_balance,a.name as agent_name, a.surname as agent_surname $sql", array(
            'totalItemCount' => $totalItemCount,
            'params' => $criteria->params,
            'sort' => array(
                'attributes' => array(
                    'number','tariff_id','operator_region_id'
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
                $ticket->save();

                NumberHistory::addHistoryNumber($number->id,'Установлен новый ICC: "'.$_POST['value'].'"');

                $cashierNumber=new CashierNumber();
                $cashierNumber->dt=new EDateTime();
                $cashierNumber->support_operator_id=loggedSupportOperatorId();
                $cashierNumber->number_id=$number->id;
                $cashierNumber->type=CashierNumber::TYPE_SELL;
                $cashierNumber->ticket_id=$ticketId;
                $cashierNumber->sum=500;
                $cashierNumber->save();

                $trx->commit();

                Yii::app()->user->setFlash('success','Номер успешно продан');
                $this->redirect(array('cashier/sellList'));
            }
        }

        $this->render('sell', array(
            'number' => $number,
            'model' => $model
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

    public function actionRestoreList() {

        $list = $this->getNumberListDataProvider($this->restoreBaseCriteria());

        $this->render('restoreList', array(
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
            $this->redirect(array('restoreList'));
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
                $ticket->save();

                NumberHistory::addHistoryNumber($number->id,'Установлен новый ICC: "'.$_POST['value'].'"');

                $cashierNumber=new CashierNumber();
                $cashierNumber->dt=new EDateTime();
                $cashierNumber->support_operator_id=loggedSupportOperatorId();
                $cashierNumber->number_id=$number->id;
                $cashierNumber->type=CashierNumber::TYPE_RESTORE;
                $cashierNumber->ticket_id=$ticketId;
                $cashierNumber->sum=100;
                $cashierNumber->save();

                $trx->commit();

                Yii::app()->user->setFlash('success','Номер успешно восстановлен');
                $this->redirect(array('cashier/restoreList'));
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

}