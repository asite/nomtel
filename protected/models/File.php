<?php

Yii::import('application.models._base.BaseFile');

class File extends BaseFile
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function getProtectionCode() {
        return substr(sha1($this->id.rand().'BWQUwXsywDnak5H8'),0,16);
    }

    public function calculateUrlDir() {
        return '/var/files'.$this->calculateSubfolder();
    }

    public function calculateDir() {
        return Yii::getPathOfAlias('webroot.var.files').$this->calculateSubfolder($this->id);
    }

    public function calculateSubfolder() {
        $padded_id = $this->id;
        while (strlen($padded_id) % 2 != 0)
            $padded_id = '0' . $padded_id;

        $folder_parts = str_split($padded_id, 2);
        array_pop($folder_parts);

        $res = '/';
        foreach ($folder_parts as $fp)
            $res.=$fp . '/';

        return $res;
    }

    public function rules()
    {
        return array_merge(parent::rules(),array(
                array('url', 'file', 'types' => 'jpg,jpeg,gif,png', 'maxSize' => 1024 * 1024 * 10, 'tooLarge' => Yii::t('app','Size should be less then 2MB !!!'), 'on' => 'upload'),
        ));
    }

}