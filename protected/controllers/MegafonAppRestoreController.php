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

        $megafonAppRestore->generateDocument(false);
   }
}