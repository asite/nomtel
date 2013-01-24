<?php

class NumberController extends BaseGxController
{
    public function additionalAccessRules() {
        return array(
            array('allow', 'actions' => array('list','view'), 'roles' => array('agent','support')),
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
        $params['BalanceReportNumber'] = BalanceReportNumber::model()->find($criteria);

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
            'BalanceReportNumber'=>$params['BalanceReportNumber'],
            'numberHistory'=>$params['numberHistory']
        ));
    }

    public function actionEdit($id)
    {
        $params = $this->getNumberInfo($id);
        $numberInfoEdit = new NumberInfoEdit;
        $tariff = Tariff::model()->findByPk($params['number']->sim->tariff_id);
        $operatorRegion = OperatorRegion::model()->findByPk($params['number']->sim->operator_region_id);
        $company = Company::model()->findByPk($params['number']->sim->company_id);

        $this->render('edit',array(
            'number'=>$params['number'],
            'sim'=>$params['sim'],
            'SubscriptionAgreement'=>$params['SubscriptionAgreement'],
            'BalanceReportNumber'=>$params['BalanceReportNumber'],
            'numberHistory'=>$params['numberHistory'],
            'tariff'=>$tariff,
            'operatorRegion'=>$operatorRegion,
            'company'=>$company
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
                NumberHistory::addHistoryNumber($number->id,'Тариф сменен с {Tariff:'.$tariff_id->tariff_id.'} на {Tariff:'.$_POST['value'].'}');
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
                NumberHistory::addHistoryNumber($number->id,'Регион сменен с {OperatorRegion:'.$region_id->operator_region_id.'} на {OperatorRegion:'.$_POST['value'].'}');
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
                NumberHistory::addHistoryNumber($number->id,'Компания сменена с {Company:'.$company_id->company_id.'} на {Company:'.$_POST['value'].'}');
            } catch (CDbException $e) {
                $this->ajaxError(Yii::t("app", "Can't delete this object because it is used by another object(s)"));
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
                $this->ajaxError(Yii::t("app", "Can't delete this object because it is used by another object(s)"));
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
                NumberHistory::addHistoryNumber($number->id,'Кодовое слово сменено с "'.$service_password.'" на "'.$_POST['value'].'"');
            } catch (CDbException $e) {
                $this->ajaxError(Yii::t("app", "Can't delete this object because it is used by another object(s)"));
            }
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }
}

