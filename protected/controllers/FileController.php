<?php
class FileController extends BaseGxController
{

    private function returnError($msg)
    {
        $data = array(array('error' => $msg));
        $this->returnData($data);
    }

    private function returnData($data)
    {
        echo function_exists('json_encode') ? json_encode($data) : CJSON::encode($data);
        Yii::app()->end();
    }

    public function actionUpload()
    {
        header('Vary: Accept');
        if (isset($_SERVER['HTTP_ACCEPT']) &&
            (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
        ) {
            header('Content-type: application/json');
        } else {
            header('Content-type: text/plain');
        }

        $data = array();

        $model = new File('upload');
        $file = CUploadedFile::getInstance($model, 'url');
        $model->url = $file;

        if ($model->url !== null && $model->validate(array('url'))) {
            $model->save();

            $dir = $model->calculateDir();
            if (!file_exists($dir))
                if (!mkdir($dir, 0755, true)) $this->returnError(Yii::t('app', "Can't create dir %dir%", array('%dir%' => $dir)));

            $fn = $model->id . '_' . $model->getProtectionCode() . '.jpg';

            $fullFn = $dir . $fn;

            if (!Thumb::process($file->tempName, $fullFn, 'file'))
                $this->returnError(Yii::t('app', "Can't resize and/or save file"));

            $model->url = $model->calculateUrlDir() . $fn;
            $model->save();

            // return data to the fileuploader
            $data[] = array(
                'name' => $file->name,
                'type' => $file->type,
                'size' => filesize($fullFn),
                // we need to return the place where our image has been saved
                'url' => $model->url, // Should we add a helper method?
                // we need to provide a thumbnail url to display on the list
                // after upload. Again, the helper method now getting thumbnail.
                'thumbnail_url' => Thumb::createUrl($model->url, 'uploader'),
                // we need to include the action that is going to delete the url
                // if we want to after loading
                'delete_url' => $this->createUrl('delete', array('id' => $model->id, 'method' => 'uploader')),
                'delete_type' => 'POST'
            );
            $this->returnData($data);
        } else {
            if ($model->hasErrors('url')) {
                $this->returnError($model->getErrors('url'));
            } else {
                throw new CHttpException(500, "Could not upload file " . CHtml::errorSummary($model));
            }
        }
    }

    public function actionDelete($id)
    {
        File::model()->deleteByPk($id);
    }

}
