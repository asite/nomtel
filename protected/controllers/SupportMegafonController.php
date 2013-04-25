<?php
class SupportMegafonController extends BaseGxController
{
    public function additionalAccessRules() {
        return array(
            // array('disallow', 'actions'=>array('indexAdmin','detailAdmin'),'roles' => array('supportMegafon')),
            array('allow', 'roles' => array('supportMegafon')),
        );
    }

    public function actionNumberList()
    {
        $model = new SupportMegafonNumberSearch();
        $model->unsetAttributes();

        if (isset($_REQUEST['SupportMegafonNumberSearch']))
            $model->setAttributes($_REQUEST['SupportMegafonNumberSearch']);

        $criteria = new CDbCriteria();
        $criteria->compare('s.operator_id',Operator::OPERATOR_MEGAFON_ID);
        $criteria->compare('n.support_status',Number::SUPPORT_STATUS_ACTIVE);
        $criteria->addCondition('s.id=s.parent_id');
        $criteria->compare('p.name',$model->name,true);
        $criteria->compare('p.surname',$model->surname,true);
        $criteria->compare('p.middle_name',$model->middle_name,true);
        $criteria->compare('n.number',$model->number,true);
        $criteria->compare('n.personal_account',$model->personal_account,true);

        $sql = "from sim s
            join number n on (n.sim_id=s.id)
            join subscription_agreement sa on (sa.number_id=n.id)
            join person p on (p.id=sa.person_id)
            where " . $criteria->condition;

        $totalItemCount = Yii::app()->db->createCommand('select count(*) ' . $sql)->queryScalar($criteria->params);

        $dataProvider = new CSqlDataProvider('select n.id,n.personal_account,n.number,p.name,p.surname,p.middle_name ' . $sql, array(
            'totalItemCount' => $totalItemCount,
            'params' => $criteria->params,
            'sort' => array(
                'attributes' => array(
                    'personal_account','number','name','surname','middle_name'
                ),
            ),
            'pagination' => array('pageSize' => Sim::ITEMS_PER_PAGE)
        ));

        $list['model'] = $model;
        $list['dataProvider'] = $dataProvider;

        $this->render('numberList', $list);
    }
}
