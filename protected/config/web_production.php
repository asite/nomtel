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
        'application.extensions.giix-components.*'
    ),
    'modules' => array(),
    // application components
    'components' => array(
        'authManager' => array(
            'class' => 'PhpAuthManager',
            'authFile' => 'protected/config/auth.php',
            'defaultRoles' => array('guest'),
        ),
        'request' => array(
            'enableCsrfValidation' => true,
            'enableCookieValidation' => true,
        ),
        'user' => array(
            'class'=>'WebUser',
            // enable cookie-based authentication
            'allowAutoLogin' => true,
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            // 'useStrictParsing' => true,
            'rules' => array(
                '/' => 'site/login',
                'var/thumbs/<url:.*>' => 'thumb/generateThumbnail',
            ),
        ),
        'assetManager' => array(
            //'linkAssets' => true,
        ),
        'clientScript' => array(),
        'db' => array(
            'charset' => 'utf8',
            'enableProfiling' => ENABLE_PROFILING,
            'schemaCachingDuration' => 60,
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
    		array(
        	    'class' => 'CProfileLogRoute',
        	    'showInFireBug' => true,
    		)
            ),
        ),
        'viewRenderer' => array(
            'class' => 'application.extensions.smarty.ESmartyViewRenderer',
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
        'thumbs'=>array(
            'file' => array(
                'width' => 1500,
                'height' => 1500,
                'resizeMode' => 'max',
                'outputFormat' => 'jpg',
                'qualityJPG' => 80,
            ),
            'uploader' => array(
                'width' => 200,
                'height' => 200,
                // available modes:
                // max - make image not larger, than width*height
                // resizeAndCrop - make image size width*height, crop parts of image if needed
                'resizeMode' => 'max',
                // available formats
                // jpg - force jpg
                // png - force png32
                // auto - if source is png/gif use png, in other cases - jpg
                'outputFormat' => 'jpg',
                // output quality for jpg images (0-100)
                'qualityJPG' => 90,
                // output quality (compression level) for png images (0-9)
                'qualityPNG' => 9,
            )
        )
    ),
);

return $config;
