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

        $text="</w:t></w:r></w:p>";

        $sim_type_names=array(
            MegafonAppRestoreNumber::SIM_TYPE_NORMAL=>'обычную',
            MegafonAppRestoreNumber::SIM_TYPE_MICRO=>'микро',
            MegafonAppRestoreNumber::SIM_TYPE_NANO=>'нано',
        );
        foreach($megafonAppRestore->megafonAppRestoreNumbers as $number) {
            $item="
                    <w:p>
                        <w:pPr>
                            <w:pStyle w:val=\"style0\"/>
                            <w:ind w:firstLine=\"493\" w:left=\"1077\" w:right=\"902\"/>
                        </w:pPr>
                        <w:r>
                            <w:rPr>
                                <w:sz w:val=\"28\"/>
                                <w:szCs w:val=\"28\"/>
                            </w:rPr>
                            <w:t xml:space=\"preserve\">Прошу восстановить </w:t>
                        </w:r>
                        <w:r>
                            <w:rPr>
                                <w:b/>
                                <w:sz w:val=\"28\"/>
                                <w:szCs w:val=\"28\"/>
                            </w:rPr>
                            <w:t>{$number->number->number}</w:t>
                        </w:r>
                        <w:r>
                            <w:rPr>
                                <w:sz w:val=\"28\"/>
                                <w:szCs w:val=\"28\"/>
                            </w:rPr>
                            <w:t xml:space=\"preserve\"> на </w:t>
                        </w:r>
                        <w:r>
                            <w:rPr>
                                <w:b/>
                                <w:sz w:val=\"28\"/>
                                <w:szCs w:val=\"28\"/>
                            </w:rPr>
                            <w:t>{$sim_type_names[$number->sim_type]}</w:t>
                        </w:r>
                        <w:r>
                            <w:rPr>
                                <w:sz w:val=\"28\"/>
                                <w:szCs w:val=\"28\"/>
                            </w:rPr>
                            <w:t xml:space=\"preserve\"> симкарту.</w:t>
                        </w:r>
                    </w:p>
                    ";
            if ($number->status!=MegafonAppRestoreNumber::STATUS_PROCESSING)
                $item=str_replace('<w:rPr>','<w:rPr><w:strike/>',$item);

            $text.=$item;
        }

        $text.="        <w:p>
            <w:pPr>
                <w:pStyle w:val=\"style0\"/>
                <w:ind w:firstLine=\"493\" w:left=\"1077\" w:right=\"902\"/>
            </w:pPr>
            <w:r>
                <w:rPr>
                    <w:sz w:val=\"28\"/>
                    <w:szCs w:val=\"28\"/>
                </w:rPr>
                <w:t xml:space=\"preserve\">";

        DocumentGenerator::generate('megafon_app_restore.docx','megafon_app_restore_'.$megafonAppRestore->dt.'.docx',array(
            'day'=>$megafonAppRestore->dt->format('d'),
            'month'=>Yii::app()->dateFormatter->format('MMMM',$megafonAppRestore->dt->getTimestamp()),
            'year'=>$megafonAppRestore->dt->format('Y'),
            'id'=>$megafonAppRestore->id,
            'text'=>$text
        ));

    }
}