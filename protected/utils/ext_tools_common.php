<?php

if ($_SERVER['PHP_AUTH_USER']!='additional' && $_SERVER['PHP_AUTH_PW']!='protection') {
    header('WWW-Authenticate: Basic realm="Please enter your credentials"');
    header('HTTP/1.0 401 Unauthorized');
    exit;
}