<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
$common_config=array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Nomtel Console Application',
	// application components
	'components'=>array(
        'db'=>array(
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

$base_config_name=array();

switch (gethostname()) {
    case 'primacomp':
    case 'primawork':
        $base_config_name='server_console_development.php';
        break;
    default:
        die('unknown hostname');
}

return CMap::mergeArray($common_config,include($base_config_name));