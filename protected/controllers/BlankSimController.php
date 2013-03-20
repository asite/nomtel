<?php
class BlankSimController extends BaseGxController
{
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

                $skippedIccs1=array();
                $skippedIccs2=array();
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
                    $blankSim->operator_region_id=$model->operator_region_id;
                    $blankSim->icc=$icc;

                    $blankSim->save();
                }

                $trx->commit();

                if (empty($skippedIccs1) && empty($skippedIccs2)) {
                    Yii::app()->user->setFlash('success','Все пустышки добавлены в базу');
                } else {
                    $msg='Не все пустышки были добавлены в базу:';
                    if (!empty($skippedIccs1)) {
                        $msg.='<br/><br/>Неверный формат icc: '.implode(',',$skippedIccs1);
                    }
                    if (!empty($skippedIccs2)) {
                        $msg.='<br/><br/>Уже имеются в базе icc: '.implode(',',$skippedIccs2);
                    }
                    Yii::app()->user->setFlash('success',$msg);
                }
                $this->refresh();
            }
        }

        $this->render('add',$data);
    }
}
