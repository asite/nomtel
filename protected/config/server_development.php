<?php

$config = CMap::mergeArray(
        require((YII_DEBUG ? 'web_debug.php' : 'web_production.php')), array(
            'components' => array(
                'db' => array(
                    'connectionString' => 'mysql:host=localhost;dbname=nomtel',
                    'username' => 'root',
                    'password' => 'root',
                ),
            ),
            'params' => array(
                'adminEmail'=>'pavimus@gmail.com'
            )
        )
);

return $config;