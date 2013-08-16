<?php

$config = CMap::mergeArray(
    require((YII_DEBUG ? 'web_debug.php' : 'web_production.php')), array(
        'components' => array(
            'db' => array(
                'connectionString' => 'mysql:host=localhost;dbname=dev',
                'username' => 'dev',
                'password' => 'zerozz',
            ),
        ),
        'params' => array(
            'adminEmail' => 'gghz@bk.ru',
            'adminEmailFrom' => 'info@asiteapp.ru',
            'numberHelpEmail' => 'alegudmail@gmail.com',
            'supportEmail' => array('alegudmail@gmail.com', '9292000001@mail.ru'),
            'supportEmailFrom' => 'info@asiteapp.ru',
            'megafonAppRestoreEmail'=>array('9292000001@mail.ru','9612082800@mail.ru'),
        )
    )
);

return $config;