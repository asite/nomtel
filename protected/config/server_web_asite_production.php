<?php

$config = CMap::mergeArray(
    require((YII_DEBUG ? 'web_debug.php' : 'web_production.php')), array(
        'components' => array(
            'db' => array(
                'connectionString' => 'mysql:host=localhost;dbname=asite_nomtel',
                'username' => 'asite_nomtel',
                'password' => 'nomtel',
            ),
        ),
        'params' => array(
            'adminEmail' => 'gghz@bk.ru',
            'adminEmailFrom' => 'info@asiteapp.ru'
        )
    )
);

return $config;