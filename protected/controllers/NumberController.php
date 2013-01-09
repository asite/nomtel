<?php

class NumberController extends BaseGxController
{
    public function actionWarnedList()
    {
        $model = new Number('list');
        $model->unsetAttributes();

        if (isset($_GET['Number']))
            $model->setAttributes($_GET['Number']);

        $model->warning = 1;

        $this->render('warnedList', array(
            'model' => $model,
            'dataProvider' => $model->search()
        ));
    }
}
