<?php
class FileController extends BaseGxController
{
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
            $filename= Yii::getPathOfAlias('webroot.var.files') . '/' . $model->id . '.jpg';
            $res = Thumb::process($file->tempName,$filename, 'file');

            if ($res) {
                $model->url = '/var/files/' . $model->id . '.jpg';
                $model->save();

                // return data to the fileuploader
                $data[] = array(
                    'name' => $file->name,
                    'type' => $file->type,
                    'size' => filesize($filename),
                    // we need to return the place where our image has been saved
                    'url' => $model->url, // Should we add a helper method?
                    // we need to provide a thumbnail url to display on the list
                    // after upload. Again, the helper method now getting thumbnail.
                    'thumbnail_url' => Thumb::createUrl($model->url, 'uploader'),
                    // we need to include the action that is going to delete the url
                    // if we want to after loading 
                    'delete_url' => $this->createUrl('delete', array('id' => $model->id, 'method' => 'uploader')),
                    'delete_type' => 'POST');
            } else {
                $data[] = array('error' => Yii::t('app', 'Unable to convert and save file'));
            }
        } else {
            if ($model->hasErrors('url')) {
                $data[] = array('error', $model->getErrors('url'));
            } else {
                throw new CHttpException(500, "Could not upload file " . CHtml::errorSummary($model));
            }
        }
        // JQuery File Upload expects JSON data
        echo json_encode($data);
    }

    public function actionDelete($id)
    {
        File::model()->deleteByPk($id);
    }

}
