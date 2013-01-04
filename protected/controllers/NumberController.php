<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pavel
 * Date: 04.01.13
 * Time: 22:28
 * To change this template use File | Settings | File Templates.
 */
class NumberController extends BaseGxController
{
    public function actionWarnedList()
    {
        $model = new Number('list');
        $model->unsetAttributes();

        if (isset($_GET['Number']))
            $model->setAttributes($_GET['Number']);

        $model->status=Number::STATUS_WARNING;

        $this->render('warnedList', array(
            'model' => $model,
            'dataProvider' => $model->search()
        ));
    }

}
