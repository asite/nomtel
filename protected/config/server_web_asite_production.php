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
            'numberHelpEmail' => 'alegudmail@gmail.com',
            'supportEmail' => array('alegudmail@gmail.com', '1@500099.ru'),
            'supportEmailFrom' => 'info@asiteapp.ru',
        )
    )
);

return $config;