<?php
class BlankSimController extends BaseGxController
{

    public function actionAdd() {
        $data=array();
        $model=new BlankSimAdd();
        $data['model']=$model;

        $this->performAjaxValidation($model);

        if (isset($_POST['BlankSimAdd'])) {
            $model->setAttributes($_POST['BlankSimAdd']);

            if ($model->validate()) {
                $trx=Yii::app()->db->beginTransaction();

                $iccs=preg_split('%[\r\n]%',$model->icc,-1,PREG_SPLIT_NO_EMPTY);

                $prefixes=IccPrefixRegion::model()->findAllByAttributes(array('operator_id'=>$model->operator_id));

                $skippedIccs1=array();
                $skippedIccs2=array();
                $skippedIccs3=array();
                foreach($iccs as $icc) {
                    $icc=trim($icc);
                    if (!preg_match('%^\d{16,20}$%',$icc)) {
                        $skippedIccs1[]="'$icc'";
                        continue;
                    }
                    if (BlankSim::model()->countByAttributes(array('icc'=>$icc))>0) {
                        $skippedIccs2[]="'$icc'";
                        continue;
                    }

                    $blankSim = new BlankSim;
                    $blankSim->type=$model->type;
                    $blankSim->operator_id=$model->operator_id;
                    $blankSim->icc=$icc;

                    foreach($prefixes as $prefix)
                        if (strpos($icc,$prefix->icc_prefix)===0) {
                            $blankSim->operator_region_id=$prefix->operator_region_id;
                            break;
                        }

                    if (!$blankSim->operator_region_id) {
                        $skippedIccs3[]="'$icc'";
                        continue;
                    }

                    $blankSim->save();
                }

                $trx->commit();

                if (empty($skippedIccs1) && empty($skippedIccs2) && empty($skippedIccs3)) {
                    Yii::app()->user->setFlash('success','Все пустышки добавлены в базу');
                } else {
                    $msg='Не все пустышки были добавлены в базу:';
                    if (!empty($skippedIccs1)) {
                        $msg.='<br/><br/>Неверный формат icc: '.implode(',',$skippedIccs1);
                    }
                    if (!empty($skippedIccs2)) {
                        $msg.='<br/><br/>Уже имеются в базе icc: '.implode(',',$skippedIccs2);
                    }
                    if (!empty($skippedIccs3)) {
                        $msg.='<br/><br/>Нету префиксов icc: '.implode(',',$skippedIccs3);
                    }
                    Yii::app()->user->setFlash('success',$msg);
                }
                $this->refresh();
            }
        }

        $this->render('add',$data);
    }

    public function actionAjaxOperatorCombo() {
        $regionList = OperatorRegion::getDropDownList();
        $res = '';
        if (!isset($regionList[$_POST['operatorId']])) {
            $regionList[$_POST['operatorId']]=array('' => 'Выбор региона');
        }
        foreach ($regionList[$_POST['operatorId']] as $k=>$v) {
            $res .= '<option value="' . $k . '">' . $v . '</option>';
        }
        echo $res;
    }

    public function actionUpdate($id) {
        $model = $this->loadModel($id, 'BlankSim');


        if (isset($_POST['BlankSim'])) {
            $model->setAttributes($_POST['BlankSim']);

            if ($model->validate()) {
                $model->save();
                $this->redirect(array('admin'));
            }
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    public function actionDelete($id) {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            try {
                $this->loadModel($id, 'BlankSim')->delete();
            } catch (CDbException $e) {
                $this->ajaxError(Yii::t("app","Can't delete this object because it is used by another object(s)"));
            }

            if (!Yii::app()->getRequest()->getIsAjaxRequest())
                $this->redirect(array('admin'));
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    private static function getSimListDataProvider($pager=true) {
        $model = new BlankSimSearch();
        $model->unsetAttributes();

        if (isset($_REQUEST['BlankSimSearch']))
            $model->setAttributes($_REQUEST['BlankSimSearch']);

        $criteria = new CDbCriteria();
        $criteria->addCondition("1=1");

        $criteria->compare('bs.type',$model->type);
        $criteria->compare('bs.icc',$model->icc,true);
        $criteria->compare('bs.operator_id',$model->operator_id);
        $criteria->compare('bs.operator_region_id',$model->operator_region_id);
        $criteria->compare('n.number',$model->number,true);
        $criteria->compare('bs.operator_region_id',$model->operator_region_id);

        if ($model->used_support_operator_id != Yii::t('app', 'NOT RESTORED'))
            $criteria->compare('bs.used_support_operator_id', $model->used_support_operator_id);
        else
            $criteria->addCondition("bs.used_support_operator_id is null");

        $sql = "from blank_sim bs
            left outer join operator o on (o.id=bs.operator_id)
            left outer join operator_region opr on (opr.id=bs.operator_region_id)
            left outer join support_operator so on (so.id=bs.used_support_operator_id)
            left outer join number n on (n.id=bs.used_number_id)
            where " . $criteria->condition;

        $totalItemCount = Yii::app()->db->createCommand('select count(*) ' . $sql)->queryScalar($criteria->params);

        $dataProvider = new CSqlDataProvider('select bs.*,o.title as operator,opr.title as operator_region,so.name,so.surname,n.number ' . $sql, array(
            'totalItemCount' => $totalItemCount,
            'params' => $criteria->params,
            'sort' => array(
                'attributes' => array(
                    'type','icc','operator_id','operator_region_id','used_dt','used_support_operator_id','number'
                ),
            ),
            'pagination' => $pager?array('pageSize' => Sim::ITEMS_PER_PAGE):false
        ));
        $list['model'] = $model;
        $list['dataProvider'] = $dataProvider;
        return $list;
    }

    public function actionAdmin() {
        $list = self::getSimListDataProvider();

        $this->render('admin', array(
            'model' => $list['model'],
            'dataProvider' => $list['dataProvider']
        ));
    }

}
