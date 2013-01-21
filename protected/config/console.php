<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
$common_config = array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'Nomtel Console Application',
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

$base_config_name = array();

switch (gethostname()) {
    case 'primacomp':
    case 'primaworkvirt':
        $base_config_name = 'server_console_development.php';
        break;
    case 'h2068869.stratoserver.net':
        $base_config_name = 'server_console_staging.php';
        break;
    case 'hawking.timeweb.ru':
        $base_config_name = 'server_web_asite_production.php';
        break;
    default:
        die('unknown hostname');
}

return CMap::mergeArray($common_config, include($base_config_name));