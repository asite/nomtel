<?php

// this is entry script for web servers

//error_reporting(E_ALL & ~E_NOTICE);
//ini_set('display_errors','on');

error_reporting(0);


define('YII_TRACE_LEVEL', 3);

if (isset($_SERVER['HTTP_HOST']))
    $_SERVER['SERVER_NAME']=$_SERVER['HTTP_HOST'];

switch ($_SERVER['SERVER_NAME']) {

        
    
    default:
      define('LANGUAGE', 'ru');
      
        define('YII_DEBUG', false);
        $config = 'server_web_asite2_production.php';
       break;
}

// include main yii file
require_once('protected/vendors/yii/'.(YII_DEBUG ? 'yii.php':'yiilite.php'));
require_once('protected/components/functions.php');

date_default_timezone_set(TIMEZONE);

Yii::createWebApplication(dirname(__FILE__) . '/protected/config/' . $config)->run();

