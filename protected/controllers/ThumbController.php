<?php

class ThumbController extends BaseGxController {

    public function additionalAccessRules()
    {
        return array(
            array('allow', 'users' => array('*')),
        );
    }

    public function actionGenerateThumbnail($url) {
        Thumb::generate($url);
    }

}