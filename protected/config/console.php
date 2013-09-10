<?php

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
define('TIMEZONE', 'Asia/Ekaterenburg');
$base_config_name = array();

switch (gethostname()) {
    case 'primacomp':
    case 'primaworkvirt':
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
    
    default:
         die('unknown domain name');
}

return CMap::mergeArray($common_config, include($base_config_name));