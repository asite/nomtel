<?php

class Sms
{
    public static function send($number,$message) {
        $number=urlencode($number);
        $message=urlencode($message);
        file_get_contents("http://api.infosmska.ru/interfaces/SendMessages.ashx?login=ghz&pwd=zerozz&phones=7$number&sender=nomtel&message=$message");

    }
}
