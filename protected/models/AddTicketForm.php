<?php


class AddTicketForm extends CFormModel
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
            'text' => 'Сообщение'
        );
    }
}
