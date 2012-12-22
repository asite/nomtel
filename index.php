<?php
// this is entry script for web servers

error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors','on');

define('YII_TRACE_LEVEL', 3);

if (isset($_SERVER['HTTP_HOST']))
    $_SERVER['SERVER_NAME']=$_SERVER['HTTP_HOST'];

switch ($_SERVER['SERVER_NAME']) {
    case 'nomtel.lan':
        define('LANGUAGE', 'ru');
        define('TIMEZONE', 'Europe/Minsk');
        define('YII_DEBUG', true);
        $config = 'server_development.php';
        break;
    case 'nomtel.gushchas.com':
        define('LANGUAGE', 'ru');
        define('TIMEZONE', 'Europe/Minsk');
        define('YII_DEBUG', true);
        $config = 'server_staging.php';
        break;
    case 'crm.nomtel':
        define('LANGUAGE', 'ru');
        define('TIMEZONE', 'Europe/Minsk');
        define('YII_DEBUG', true);
        $config = 'local_asite.php';
        break;
    case 'nomtel.asiteapp.ru':
        define('LANGUAGE', 'ru');
        define('TIMEZONE', 'Europe/Minsk');
        define('YII_DEBUG', true);
        $config = 'server_asite.php';
        break;
    default:
        die('unknown domain name');
        break;
}

// include main yii file
require_once(dirname(__FILE__) . '/protected/vendors/yii/'.(YII_DEBUG ? 'yii.php':'yiilite.php'));

date_default_timezone_set(TIMEZONE);

Yii::createWebApplication(dirname(__FILE__) . '/protected/config/' . $config)->run();

