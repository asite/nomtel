<?php

class Sms
{
    public static function send($number,$message) {
        if (YII_DEBUG) {
            Yii::log("SMS to number $number: $message");
            return;
        }

        $number=urlencode($number);
        $message=urlencode($message);
        file_get_contents("http://api.infosmska.ru/interfaces/SendMessages.ashx?login=ghz&pwd=zerozz&phones=7$number&sender=nomtel&message=$message");

    }
}
