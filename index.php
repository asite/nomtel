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
        $config = 'server_web_development.php';
        break;

    case 'po.nomtel.lan':
        define('LANGUAGE', 'ru');
        define('TIMEZONE', 'Europe/Minsk');
        define('YII_DEBUG', true);
        $config = 'server_web_po_development.php';
        break;

    case 'nomtel.asiteplace.ru':
        define('LANGUAGE', 'ru');
        define('TIMEZONE', 'Asia/Yeakaterinburg');
        define('YII_DEBUG', false);        
        $config = 'server_web_asite_production.php';
        break;

    case 'www.500099.ru':
    case '500099.ru':
        define('LANGUAGE', 'ru');
        define('TIMEZONE', 'Asia/Yeakaterinburg');
        define('YII_DEBUG', false);
        $config = 'server_web_po_asite_production.php';
        break;

    case 'lkdev.asiteplace.ru':
        define('LANGUAGE', 'ru');
        define('TIMEZONE', 'Asia/Yeakaterinburg');
        define('YII_DEBUG', false);
        $config = 'server_web_po_asite_development.php';
        break;

    case 'dev.asiteplace.ru':
        define('LANGUAGE', 'ru');
        define('TIMEZONE', 'Asia/Yeakaterinburg');
        define('YII_DEBUG', true);
        $config = 'server_web_asite_development.php';
        break;

    default:
        die('unknown domain name');
        break;
}

// include main yii file
require_once('protected/vendors/yii/'.(YII_DEBUG ? 'yii.php':'yiilite.php'));
require_once('protected/components/functions.php');

date_default_timezone_set(TIMEZONE);

Yii::createWebApplication(dirname(__FILE__) . '/protected/config/' . $config)->run();

