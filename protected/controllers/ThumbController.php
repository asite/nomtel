<?php

class ThumbController extends BaseGxController {

    public function actionGenerateThumbnail($url) {
        Thumb::generate($url);
    }

}