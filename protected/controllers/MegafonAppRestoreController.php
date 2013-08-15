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

        $prefix="</w:t></w:r></w:p>";
        $suffix="        <w:p>
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
        $sample="
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
                <w:t>9292000003</w:t>
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
                <w:t>микро</w:t>
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
        $text='';
        for($i=0;$i<200;$i++) if ($i%2==0) $text.=$sample;else $text.=str_replace('<w:rPr>','<w:rPr><w:strike/>',$sample);
        DocumentGenerator::generate('megafon_app_restore.docx','megafon_app_restore_'.$megafonAppRestore->dt.'.docx',array(
            'day'=>$megafonAppRestore->dt->format('d'),
            'month'=>Yii::app()->dateFormatter->format('MMMM',$megafonAppRestore->dt->getTimestamp()),
            'year'=>$megafonAppRestore->dt->format('Y'),
            'id'=>$megafonAppRestore->id,
            'text'=>$prefix.$text.$suffix
        ));

    }
}