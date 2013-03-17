<?php

class NumberController extends BaseGxController
{
    public function additionalAccessRules() {
        return array(
            array('allow', 'actions' => array('list','view'), 'roles' => array('agent')),
            array('allow', 'roles' => array('editNumberCard')),
            array('allow', 'roles' => array('support')),
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

        return $params;
    }

    public function actionView($id)
    {
        $params = $this->getNumberInfo($id);

        $this->render('view',array(
            'number'=>$params['number'],
            'sim'=>$params['sim'],
            'SubscriptionAgreement'=>$params['SubscriptionAgreement'],
            'BonusReportNumber'=>$params['BonusReportNumber'],
            'numberHistory'=>$params['numberHistory']
        ));
    }

    public function actionEdit($id)
    {
        $params = $this->getNumberInfo($id);
        $numberInfoEdit = new NumberInfoEdit;

        $this->render('edit',array(
            'number'=>$params['number'],
            'sim'=>$params['sim'],
            'SubscriptionAgreement'=>$params['SubscriptionAgreement'],
            'BalanceReportNumber'=>$params['BalanceReportNumber'],
            'numberHistory'=>$params['numberHistory'],
        ));
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
                NumberHistory::addHistoryNumber($number->id,'Кодовое слово сменено с "'.$codeword.'"" на "'.$_POST['value'].'"');
            } catch (CDbException $e) {
                $this->ajaxError(Yii::t("app", "Error"));
            }
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    public function actionSaveServicePassword($id) {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            try {
                $number = Number::model()->findByPk($id);
                $service_password = $number->service_password;
                $number->service_password = $_POST['value'];
                $number->save();

                $message = 'Прошу сделать восстановление сервис гид "'.$_POST['value'].'" и внести его в карточку номера';
                Ticket::addMessage($number->id,$message);

                if ($company_id->company_id)
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

                $number = Number::model()->findByPk($id);

                $criteria = new CDbCriteria();
                $criteria->addCondition('parent_id=:sim_id');
                $criteria->params = array(
                    ':sim_id' => $number->sim_id
                );

                Sim::model()->updateAll(array('icc' => $_POST['value']), $criteria);

                $message = "Заменить у номера ".$number->number." ICC на ".$_POST['value'];
                Ticket::addMessage($number->id,$message);

                NumberHistory::addHistoryNumber($number->id,'Установлен новый ICC: "'.$_POST['value'].'"');
            try {

            } catch (CDbException $e) {
                $this->ajaxError(Yii::t("app", "Error"));
            }
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    public function actionFree($id) {
        $trx=Yii::app()->db->beginTransaction();

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

        Yii::app()->user->setFlash('success','Номер освобожден');

        $trx->commit();

        $this->redirect(Yii::app()->request->urlReferrer);
    }
}

