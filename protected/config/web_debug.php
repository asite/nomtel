<?php

$config = include('web_production.php');


$config['modules']['gii'] = array(
    'class' => 'system.gii.GiiModule',
    'generatorPaths' => array(
        'application.extensions.giix-core', // giix generators
        'bootstrap.gii'
    ),
    'password' => 'deveLOPer',
    'ipFilters' => array('127.0.0.1', '192.168.*.*'),
);

$config['components']['db'] = array(
    'charset' => 'utf8',
    'enableProfiling' => true,
    'enableParamLogging' => true,
);

$config['components']['log'] = array(
    'class' => 'CLogRouter',
    'routes' => array(
        array(
            'class' => 'CFileLogRoute',
            'levels' => 'info, error, warning',
        ),
        array(
            'class' => 'CWebLogRoute',
            'showInFireBug' => true,
        ),
        array(
            'class' => 'CProfileLogRoute',
            'showInFireBug' => true,
        )
    ),
);

$config['import'][] = 'application.extensions.giix-components.*';

return $config;
