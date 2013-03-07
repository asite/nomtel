<?php


class OtherQuestion extends CFormModel
{
    public $text;

    public function rules() {
        return array(
            array('text','required')
        );
    }

    public function attributeLabels()
    {
        return array(
            'text' => Yii::t('app', 'Other question')
        );
    }
}
