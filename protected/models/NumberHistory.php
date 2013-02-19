<?php

Yii::import('application.models._base.BaseNumberHistory');

class NumberHistory extends BaseNumberHistory
{
    static $linksForShortcuts = array(
        'Agent'=>true,
        'Act'=>true,
        'SupportOperator'=>true,
    );


	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function addHistoryNumber($number_id,$comment,$who=null) {
        if (!isset($who)) {
            switch (Yii::app()->user->role) {
                case 'admin':
                    $who='База';
                    break;
                case 'agent':
                    $who='Агент {Agent:'.loggedAgentId().'}';
                    break;
                case 'number':
                    $who='Номер {Number:'.loggedNumberId().'}';
                    break;
                case 'support':
                    $who='Техподдержка {SupportOperator:'.loggedSupportOperatorId().'}';
            }
        }
        $model = new NumberHistory;
        $model->number_id = $number_id;
        $model->dt = new EDateTime();
        $model->who = $who;
        $model->comment = $comment;
        $model->save();
    }

    public static function getModelShortcut($model) {
        return '{'.get_class($model).':'.$model->id.'}';
    }

    public static function deleteByApproximateDtAndSubstringOrModel($dt,$subStringOrModel) {
        if ($subStringOrModel instanceof CActiveRecord) $substringOrModel=self::getModelShortcut($subStringOrModel);
        if (!$dt instanceof EDateTime) $dt=new EDateTime($dt);

        Yii::app()->db->createCommand("
            delete from number_history
            where (who like(:pattern) or comment like(:pattern)) and
                dt>=:dt_from and dt<=:dt_to
        ")->execute(array(
            ':pattern'=>'%'.$substringOrModel.'%',
            ':dt_from'=>$dt->modifiedCopy("-1 hour")->toMysqlDateTime(),
            ':dt_to'=>$dt->modifiedCopy("+1 hour")->toMysqlDateTime()
        ));
    }

    public function search()
    {
        $data_provider = parent::search();

        $data_provider->setSort(array(
            'defaultOrder' => 'dt DESC'
        ));
        return $data_provider;
    }

    private static function parseStr($string) {
        $class=$string[1];
        $model=$class::model()->findByPk($string[2]);
        if (isset(self::$linksForShortcuts[$class]))
            return '"'.CHtml::link($model,Yii::app()->createUrl(lcfirst($string[1]).'/view',array('id'=>$string[2]))).'"';
        else
            return '"'.CHtml::encode($model).'"';
    }

    public function replaceShortcutsByLinks($str='') {
        return preg_replace_callback("%{([a-zA-Z0-9]+):(\d+)}%",'NumberHistory::parseStr',$str);
    }
}