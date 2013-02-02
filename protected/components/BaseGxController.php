<?php

class BaseGxController extends GxController
{

    public $layout = '/layout/main';

    public function ajaxError($msg)
    {
        header('HTTP/1.1 500 Internal Server Error');
        echo $msg;
        Yii::app()->end();
    }

    public function checkPermissions($operation,$params,$allowCaching=true) {
        if (!Yii::app()->user->checkAccess($operation,$params,$allowCaching))
            throw new CHttpException(403,Yii::t('app','You are not authorized to perform this action.'));
    }

    /*
        public function preProcessUploadedFile($file, &$model, $field, $force_name = '') {
            if (!isset($file['error'][$field]))
                return;//throw new CException(Yii::t('app', "Can't find uploaded file info"));
            if ($file['error'][$field] == 4)
                return;
            if ($file['error'][$field] != 0)
                throw new CException(Yii::t('app', "File uploaded with error %error%", array('%error%' => $file['error'][$field])));

            if ($force_name != '') {
                preg_match('/([^.]+)$/', $file['name'][$field], $m);
                $name = $force_name . '.' . strtolower($m[1]);
            } else {
                $name = $file['name'][$field];
            }

            $name = rawurlencode($name);
            $dir=$model->getFilesSubfolder();
            $model->$field = $model->getFilesUrl() . rawurlencode($name);
        }

        public function postProcessUploadedFile($file, &$model, $field, $force_name = '') {
            if (!isset($file['error'][$field]))
                return;//throw new CException(Yii::t('app', "Can't find uploaded file info"));
            if ($file['error'][$field] == 4)
                return;
            if ($file['error'][$field] != 0)
                throw new CException(Yii::t('app', "File uploaded with error %error%", array('%error%' => $file['error'][$field])));

            umask(0);
            $dir=$model->getFilesSubfolder();

            if (!is_dir(Yii::app()->params['varDir'] . $dir))
                if (!@mkdir(Yii::app()->params['varDir'] . $dir, 0777, true))
                    throw new CException(Yii::t('app', "Can't create directory %dir%", array('%dir%' => Yii::app()->params['varDir'] . $dir)));

            if ($force_name != '') {
                preg_match('/([^.]+)$/', $file['name'][$field], $m);
                $name = $force_name . '.' . strtolower($m[1]);
            } else {
                $name = $file['name'][$field];
            }

            $name = rawurlencode($name);

            if (!@move_uploaded_file($file['tmp_name'][$field], Yii::app()->params['varDir'] . $dir . $name))
                throw new CException(Yii::t('app', "Can't move file from %from% to %to%",
                        array('%from%' => $file['tmp_name'][$field], '%to%' => Yii::app()->params['varDir'] . $dir . $name)));

            $model->$field = $model->getFilesUrl() . rawurlencode($name) . '?' . time();
        }
    */
}