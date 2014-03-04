<?php

error_reporting(E_ALL & ~E_NOTICE);

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
$common_config = array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'Telecom Agent Console Application',
    // application components
    'components' => array(
        'db' => array(
            'charset' => 'utf8',
        ),
    ),
    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.commands.*',
        'application.extensions.giix-components.*'
    ),
);

define('TIMEZONE', 'Asia/Yekaterinburg');
$base_config_name = array();

switch (gethostname()) {
    case 'primacomp':
    case 'primaworkvirt':
    case 'air-pavel.lan':
        $base_config_name = 'server_console_development.php';
        break;
    case 'h2068869.stratoserver.net':
        $base_config_name = 'server_console_staging.php';
        break;
   
    case 'www.asiteplace.ru':
        $base_config_name = 'server_console_asite_production.php';
        
    if(strpos(dirname(__FILE__), 'dev')) $base_config_name = 'server_console_asite_development.php';
        break;
        
    case 'www.valvit.ru':
        $base_config_name = 'server_web_asite2_production.php';
        break;

    case 'www.061211.ru':
	date_default_timezone_set('Europe/Moscow');
        $base_config_name = 'server_web_asite3_production.php';
	break;    
    default:
         die('unknown domain name');
}

date_default_timezone_set(TIMEZONE);

return CMap::mergeArray($common_config, include($base_config_name));