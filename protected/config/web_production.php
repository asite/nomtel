<?php

define('ENABLE_PROFILING', isset($_COOKIE['profiling']));

if (isset($_GET['profiling']))
    setcookie('profiling', 'true', 0, '/');

// contains debug settings for web servers

Yii::setPathOfAlias('vendors', dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' .
    DIRECTORY_SEPARATOR . 'vendors');

return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'Nomtel CRM',
    'runtimePath' => dirname(__FILE__) . '/../../var/runtime',
    'sourceLanguage' => 'en',
    'language' => 'ru',
    'preload' => array(
        'log',
        'bootstrap',
    ),
    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.commands.*',
        'application.vendors.*',
        'application.extensions.yii-mail.YiiMailMessage',
    ),
    'modules' => array(
    ),
    // application components
    'components' => array(
        'request' => array(
            'enableCsrfValidation' => true,
            'enableCookieValidation' => true,
        ),
        'user' => array(
            // enable cookie-based authentication
            'allowAutoLogin' => true,
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            // 'useStrictParsing' => true,
            'rules' => array(
                '/' => 'site/login',
            ),
        ),
        'assetManager' => array(
            'linkAssets' => true,
        ),
        'clientScript' => array(
        ),
        'db' => array(
            'charset' => 'utf8',
            'enableProfiling' => ENABLE_PROFILING,
            'schemaCachingDuration' => 3600,
        ),
        'cache' => array(
            'class' => 'CFileCache',
        ),
        'errorHandler' => array(
            'errorAction' => 'site/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
            ),
        ),
        'viewRenderer'=>array(
            'class'=>'application.extensions.smarty.ESmartyViewRenderer',
        ),
        'mail' => array(
            'class' => 'application.extensions.yii-mail.YiiMail',
            'transportType' => 'php',
            'viewPath' => 'application.views.mail',
        ),
        'bootstrap' => array(
            'class' => 'ext.bootstrap.components.Bootstrap',
            'responsiveCss' => true,
        ),
    ),
    'params' => array(
        'varUrl' => '/var/',
        'varDir' => dirname(__FILE__) . '/../../var/',
        'simPrice' => 100,
    ),
);

return $config;
