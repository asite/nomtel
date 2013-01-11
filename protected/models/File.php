<?php

Yii::import('application.models._base.BaseFile');

class File extends BaseFile
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function getUrlR() {
        return $this->url;
    }

    public function rules()
    {
        return array_merge(parent::rules(),array(
                array('url', 'file', 'types' => 'jpg,jpeg,gif,png', 'maxSize' => 1024 * 1024 * 10, 'tooLarge' => Yii::t('app','Size should be less then 2MB !!!'), 'on' => 'upload'),
        ));
    }

}