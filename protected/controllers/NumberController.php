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
        $criteria->select = "t.*";
        $criteria->join = "JOIN ".Number::model()->tableName()." ON t.parent_id=number.sim_id";
        $criteria->condition = "t.number=:number";
        $criteria->params= array(":number" => $number->number);
        $criteria->order = "t.id DESC";
        $criteria->limit = 1;

        $sim = Sim::model()->find($criteria);

        $criteria = new CDbCriteria();
        $criteria->select = "t.*";
        $criteria->join = "JOIN ".Number::model()->tableName()." ON t.number_id=number.id";
        $criteria->condition = "number.id=:id";
        $criteria->params= array(":id" => $number->id);
        $criteria->order = "number.id DESC";
        $criteria->limit = 1;

        $SubscriptionAgreement = SubscriptionAgreement::model()->find($criteria);

        $numberHistory = new NumberHistory('search');
        $numberHistory->number_id = $number->id;

        $this->render('view',array(
            'number'=>$number,
            'sim'=>$sim,
            'SubscriptionAgreement'=>$SubscriptionAgreement,
            'numberHistory'=>$numberHistory
        ));
    }
}
