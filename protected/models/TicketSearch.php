<?php

class TicketSearch extends CFormModel
{
    public $number;
    public $dt;
    public $status;

    public function rules() {
        return array(
            array('number,date,status','safe'),
            array('dt','date','format'=>'dd.MM.yyyy')
        );
    }

    public function attributeLabels() {
        return array(
            'number'=>Yii::t('app','Number'),
            'dt'=>Yii::t('app','Dt'),
            'status'=>Yii::t('app','Status')
        );
    }

    public static function getSqlDataProvider($criteria) {
        $model=new TicketSearch();

        if ($_GET['TicketSearch'])
            $model->setAttributes($_GET['TicketSearch']);


        $criteria->compare('n.number',$model->number,true);

        if ($model->dt && $model->validate(array('dt'))) {
            $dt=new EDateTime($model->dt);
            $criteria->params[':start_dt']=$dt->toMysqlDateTime();
            $criteria->params[':end_dt']=$dt->modifiedCopy("+1 day")->toMysqlDateTime();
            $criteria->addCondition('t.dt>=:start_dt');
            $criteria->addCondition('t.dt<:end_dt');
        }

        $criteria->compare('t.status',$model->status);

        $sql="from ticket t
                join number n on (n.id=t.number_id)
                join sim s on (s.id=t.sim_id)
                join operator o on (o.id=s.operator_id)
                join tariff ta on (ta.id=s.tariff_id)";

        $condition=$criteria->condition;

        if ($condition) $sql.=" where ".$condition;

        $totalItemCount = Yii::app()->db->createCommand('select count(*) ' . $sql)->queryScalar($criteria->params);

        $dataProvider = new CSqlDataProvider('select t.*,n.number,n.balance_status,o.title as operator_title,ta.title as tariff_title ' . $sql, array(
            'totalItemCount' => $totalItemCount,
            'params' => $criteria->params,
            'sort' => array(
            ),
            'pagination' => array(
                'pageSize' => Ticket::ITEMS_PER_PAGE
            )
        ));

        return array($dataProvider,$model);
    }
}
