<?php

/**
 * Helps bridge the gap between php 5.2 and 5.3 DateTime class and also implants
 * __toString() to spit out a MySQL datetime string
 */
class EDateTime extends DateTime {

    public static $DBFormat = 'Y-m-d H:i:s';

    public function getTimestamp() {
        return method_exists('DateTime', 'getTimestamp') ? parent::getTimestamp() : $this->format('U');
    }

    function setTimestamp($timestamp) {
        if (method_exists('DateTime', 'setTimestamp'))
            parent::setTimestamp($timestamp);

        $thisz_original = $this->getTimezone()->getName();
        $thisz_utc = new DateTimeZone('UTC');
        $this->setTimezone($thisz_utc);
        $year = gmdate("Y", $timestamp);
        $month = gmdate("n", $timestamp);
        $day = gmdate("j", $timestamp);
        $hour = gmdate("G", $timestamp);
        $minute = gmdate("i", $timestamp);
        $second = gmdate("s", $timestamp);
        $this->setDate($year, $month, $day);
        $this->setTime($hour, $minute, $second);
        $this->setTimezone(new DateTimeZone($thisz_original));
    }

    public function __toString() {
        return (string) parent::format(self::$DBFormat);
    }

}