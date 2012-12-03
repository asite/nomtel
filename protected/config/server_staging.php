<?php

$config = CMap::mergeArray(
        require((YII_DEBUG ? 'web_debug.php' : 'web_production.php')), array(
            'components' => array(
                'db' => array(
                    'connectionString' => 'mysql:host=localhost;dbname=nomtel',
                    'username' => 'www_nomtel',
                    'password' => '9Z4b764fY0WzglOocmeB',
                ),
            ),
        )
);

return $config;