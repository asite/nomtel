<?php

class MegafonAppRestoreController extends BaseGxController
{

    public function actionList()
    {
        $model = new MegafonAppRestore('search');
        $model->unsetAttributes();

        if (isset($_GET['MegafonAppRestore']))
            $model->setAttributes($_GET['MegafonAppRestore']);

        $this->render('list', array(
            'model' => $model,
        ));
    }

    public function actionDownload($id) {
        $megafonAppRestore=$this->loadModel($id,'MegafonAppRestore');

        $megafonAppRestore->generateDocument(false);
    }

    private static function getProcessingNumbersDataProvider() {
        $model = new MegafonAllRestoreNumberSearch();
        $model->unsetAttributes();

        if (isset($_REQUEST['MegafonAllRestoreNumberSearch']))
            $model->setAttributes($_REQUEST['MegafonAllRestoreNumberSearch']);

        $criteria = new CDbCriteria();
        $criteria->compare('marn.status',MegafonAppRestoreNumber::STATUS_PROCESSING);
        $criteria->compare('n.number',$model->number,true);
        if ($model->dt!='') {
            $dt=new EDateTime($model->dt);
            $criteria->compare('mar.dt',$dt->toMysqlDate());
        }
        $criteria->compare('mar.id',$model->id);

        $sql = "from megafon_app_restore_number marn
            join megafon_app_restore mar on (mar.id=marn.megafon_app_restore_id)
            join number n on (n.id=marn.number_id)
            where " . $criteria->condition;

        $totalItemCount = Yii::app()->db->createCommand('select count(*) ' . $sql)->queryScalar($criteria->params);

        $dataProvider = new CSqlDataProvider('select n.number,mar.dt,mar.id ' . $sql, array(
            'totalItemCount' => $totalItemCount,
            'params' => $criteria->params,
            'sort' => array(
                'attributes' => array(
                    'number','dt','id'
                ),
            ),
            'pagination' => array('pageSize' => Sim::ITEMS_PER_PAGE)
        ));

        return array('model'=>$model,'dataProvider'=>$dataProvider);
    }

    public function restoreNumberForClient($number,$icc) {
        Sim::model()->updateAll(array('icc'=>$icc),'parent_id=:parent_id',array(':parent_id'=>$number->sim_id));
    }

    public function restoreNumberForSelling($number,$icc) {
        $sim=Sim::model()->findByAttributes(array(
            'parent_id'=>$number->sim_id,
            'agent_id'=>null
        ));

        $parent_id=$sim->parent_id;

        // create new sim with same data, binded to base
        $sim->unsetAttributes(array('id','parent_act_id','agent_id','parent_id'));
        $sim->parent_agent_id=adminAgentId();
        $sim->isNewRecord=true;
        $sim->icc=$icc;
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
    }

    public function actionProcess() {
        $processModel=new MegafonAppRestoreNumberProcess();

        if (isset($_POST['MegafonAppRestoreNumberProcess'])) {
            $processModel->setAttributes($_POST['MegafonAppRestoreNumberProcess']);

            if ($processModel->validate()) {
                $number=Number::model()->findByAttributes(array('number'=>$processModel->number));
                if ($number) {
                    $megafonAppRestoreNumber=MegafonAppRestoreNumber::model()->findByAttributes(array('number_id'=>$number->id,'status'=>MegafonAppRestoreNumber::STATUS_PROCESSING));
                    if ($megafonAppRestoreNumber) {
                        $trx=Yii::app()->db->beginTransaction();
                        $megafonAppRestoreNumber->status=MegafonAppRestoreNumber::STATUS_DONE;
                        $megafonAppRestoreNumber->save();

                        NumberHistory::addHistoryNumber($number->id,'Номер успешно восстановлен по заявлению №'.$megafonAppRestoreNumber->megafonAppRestore->id.' от '.$megafonAppRestoreNumber->megafonAppRestore->dt->format('d.m.Y'));

                        if ($megafonAppRestoreNumber->restore_for_selling)
                            $this->restoreNumberForSelling($number,$processModel->icc);
                        else
                            $this->restoreNumberForClient($number,$processModel->icc);

                        $trx->commit();

                        Yii::app()->user->setFlash('success','Восстановление номера прошло успешно');
                        $this->refresh();
                    }
                }
                $processModel->addError('number','Данный номер не находится на восстановлении');
            }

        }
        $data=self::getProcessingNumbersDataProvider();
        $data['processModel']=$processModel;

        $this->render('process', $data);
    }
}