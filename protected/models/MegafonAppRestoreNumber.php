<?php

Yii::import('application.models._base.BaseMegafonAppRestoreNumber');

class MegafonAppRestoreNumber extends BaseMegafonAppRestoreNumber
{
    const SIM_TYPE_NORMAL='NORMAL';
    const SIM_TYPE_MICRO='MICRO';
    const SIM_TYPE_NANO='NANO';

    const STATUS_PROCESSING='PROCESSING';
    const STATUS_DONE='DONE';
    const STATUS_REJECTED='REJECTED';

    public function rules() {
        return array_merge(parent::rules(),array(
            array('sum,contact_phone,contact_name','required','on'=>'number_restore_by_cashier_with_sum'),
            array('contact_phone,contact_name','required','on'=>'number_restore_by_cashier_without_sum'),
        ));
    }

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public static function getSimTypeRadioList($items=array()) {
        $labels=self::getSimTypeLabels();
        return array_merge($items,$labels);
    }

    public static function getSimTypeLabels() {
        static $labels;

        if (!$labels) {
            $labels=array(
                self::SIM_TYPE_NORMAL=>Yii::t('app','BLANK_SIM_TYPE_NORMAL'),
                self::SIM_TYPE_MICRO=>Yii::t('app','BLANK_SIM_TYPE_MICRO'),
                self::SIM_TYPE_NANO=>Yii::t('app','BLANK_SIM_TYPE_NANO')
            );
        }

        return $labels;
    }

    public static function getSimTypeLabel($status) {
        $labels=self::getSimTypeLabels();
        return $labels[$status];
    }

    public function afterSave() {
        parent::afterSave();

        $this->megafonAppRestore->numbers_count=self::model()->countByAttributes(array('megafon_app_restore_id'=>$this->megafonAppRestore->id));
        $this->megafonAppRestore->unprocessed_numbers_count=self::model()->countByAttributes(array('megafon_app_restore_id'=>$this->megafonAppRestore->id,'status'=>self::STATUS_PROCESSING));
        $this->megafonAppRestore->save();
    }

}