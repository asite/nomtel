<?php

Yii::import('application.models._base.BaseMegafonAppRestore');

class MegafonAppRestore extends BaseMegafonAppRestore
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public static function getCurrent() {
        $now=new EDateTime();
        $now->setTimezone(new DateTimeZone('Europe/Moscow'));
        if ($now->format('H')<4) $now->modify('-1 DAY');

        $model=self::model()->findByAttributes(array('dt'=>$now->toMysqlDate()));

        if (!$model) {
            $model=new MegafonAppRestore();
            $model->dt=$now->toMysqlDate();
            $model->save();
        }

        return $model;
    }

    static private function generateDocumentNumbersText($numbers) {
        $text="</w:t></w:r></w:p>";

        $sim_type_names=array(
            MegafonAppRestoreNumber::SIM_TYPE_NORMAL=>'обычную',
            MegafonAppRestoreNumber::SIM_TYPE_MICRO=>'микро',
            MegafonAppRestoreNumber::SIM_TYPE_NANO=>'нано',
        );

        foreach($numbers as $number) {
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

        return $text;
    }

    public function generateDocument($storeToDisk) {
        $filename=DocumentGenerator::generate('megafon_app_restore.docx','megafon_app_restore_'.$this->dt.'.docx',array(
            'day'=>$this->dt->format('d'),
            'month'=>Yii::app()->dateFormatter->format('MMMM',$this->dt->getTimestamp()),
            'year'=>$this->dt->format('Y'),
            'id'=>$this->id,
            'text'=>self::generateDocumentNumbersText($this->megafonAppRestoreNumbers)
        ),$storeToDisk);

        return $filename;
    }

    static public function generateUnrestoredReport($storeToDisk) {
        $currentMegafonAppRestore=self::getCurrent();

        $numbers=MegafonAppRestoreNumber::model()->findAllBySql(
            'select * from megafon_app_restore_number where megafon_app_restore_id<:id and status=:status order by id asc',
            array(
                ':id'=>$currentMegafonAppRestore->id-1,
                ':status'=>MegafonAppRestoreNumber::STATUS_PROCESSING,
            ));

        $filename=DocumentGenerator::generate('megafon_app_restore.docx','megafon_unrestored_summary_'.$currentMegafonAppRestore->dt.'.docx',array(
            'day'=>$currentMegafonAppRestore->dt->format('d'),
            'month'=>Yii::app()->dateFormatter->format('MMMM',$currentMegafonAppRestore->dt->getTimestamp()),
            'year'=>$currentMegafonAppRestore->dt->format('Y'),
            'id'=>$currentMegafonAppRestore->id.'-NOT-PROCESSED',
            'text'=>self::generateDocumentNumbersText($numbers)
        ),$storeToDisk);

        return $filename;
    }
}