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
            'adminPhone' => '9292000003',
            'adminEmailFrom' => 'info@nomtel.com',
            'numberHelpEmail' => 'yurka.god@gmail.com',
            'supportEmail' => array('pavimus@gmail.com','pavimus@gmail.com'),
            'supportEmailFrom' => 'info@asiteapp.ru',
            'megafonBalanceEmails' => array(
                'host'=>'mail.asiteplace.ru',
                'port'=>'143',
                'type'=>'imap',
                'ssl'=>true,
                'inBOX'=>'INBOX',
                'processedBOX'=>'INBOX.processed',
                'skippedBOX'=>'INBOX.skipped',
                'login'=>'robot@asiteplace.ru',
                'password'=>'XWfVmXW4'
            )
        )
    )
);

return $config;