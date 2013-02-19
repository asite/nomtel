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
            'adminEmail' => 'pavimus@gmail.com',
            'adminEmailFrom' => 'info@nomtel.com',
            'supportEmail' => array('pavimus@gmail.com','pavimus@gmail.com'),
            'supportEmailFrom' => 'info@asiteapp.ru',
            'megafonBalanceEmails' => array(
                'host'=>'mail.asiteplace.ru',
                'port'=>'110',
                'type'=>'pop3',
                'ssl'=>true,
                'login'=>'robot@asiteplace.ru',
                'password'=>'XWfVmXW4'
            )
        )
    )
);

return $config;