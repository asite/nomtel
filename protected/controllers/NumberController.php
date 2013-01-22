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

    public function actionView($id)
    {
        $number=$this->loadModel($id,'Number');

        //$agent = Yii::app()->db->createCommand("SELECT s.* FROM ".Sim::model()->tableName()." s JOIN ".Number::model()->tableName().
        //    " n ON s.parent_id=n.sim_id WHERE s.number=:number ORDER BY s.id DESC LIMIT 1")->queryAll(true, array(':number'=>$number->number));


        $criteria = new CDbCriteria();
        $criteria->condition = "parent_id=:sim_id";
        $criteria->params= array(":sim_id" => $number->sim_id);
        $criteria->order = "id DESC";
        $criteria->limit = 1;
        $sim = Sim::model()->find($criteria);

        $criteria = new CDbCriteria();
        $criteria->condition = "number_id=:id";
        $criteria->params= array(":id" => $number->id);
        $criteria->order = "id DESC";
        $criteria->limit = 1;
        $SubscriptionAgreement = SubscriptionAgreement::model()->find($criteria);

        $criteria = new CDbCriteria();
        $criteria->condition = "number_id=:id";
        $criteria->params= array(":id" => $number->id);
        $criteria->order = "id DESC";
        $criteria->limit = 1;
        $BalanceReportNumber = BalanceReportNumber::model()->find($criteria);

        $numberHistory = new NumberHistory('search');
        $numberHistory->number_id = $number->id;

        $this->render('view',array(
            'number'=>$number,
            'sim'=>$sim,
            'SubscriptionAgreement'=>$SubscriptionAgreement,
            'BalanceReportNumber'=>$BalanceReportNumber,
            'numberHistory'=>$numberHistory
        ));
    }
}

