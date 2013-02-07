<?php

$config = CMap::mergeArray(
    require((YII_DEBUG ? 'web_debug.php' : 'web_production.php')), array(
        'components' => array(
            'db' => array(
                'connectionString' => 'mysql:host=localhost;dbname=crm',
                'username' => 'test',
                'password' => 'sMJngolM',
            ),
        ),
        'params' => array(
            'adminEmail' => 'gghz@bk.ru',
            'adminEmailFrom' => 'info@asiteapp.ru',
            'supportEmail' => array('alegudmail@gmail.com', '9292000001@mail.ru'),
            'supportEmailFrom' => 'info@asiteapp.ru'
        )
    )
);

return $config;