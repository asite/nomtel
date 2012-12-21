<?php

$config = CMap::mergeArray(
        require((YII_DEBUG ? 'web_debug.php' : 'web_production.php')), array(
            'components' => array(
                'db' => array(
                    'connectionString' => 'mysql:host=localhost;dbname=crm_nomtel',
                    'username' => 'root',
                    'password' => '',
                ),
            ),
            'params' => array(
                'adminEmail'=>'gghz@bk.ru',
		      'adminEmailFrom'=>'info@nomtel.com'
            )
        )
);

return $config;