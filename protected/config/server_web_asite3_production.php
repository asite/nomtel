<?php

$config = CMap::mergeArray(
    require((YII_DEBUG ? 'web_debug.php' : 'web_production.php')), array(
        'components' => array(
            'db' => array(
                'connectionString' => 'mysql:host=localhost;dbname=crm',
                'username' => 'crm',
                    'password' => 'Nowiet123',
            ),
        ),
        'params' => array(
            'adminEmail' => 'gghz@bk.ru',
            'adminPhone' => '9292000003',
            'adminEmailFrom' => 'info@asiteapp.ru',
            'numberHelpEmail' => 'alegudmail@gmail.com',
            'supportEmail' => array('alegudmail@gmail.com'),
            'supportEmailFrom' => 'info@asiteapp.ru',
            'megafonBalanceEmails' => array(
                'host'=>'mail.061211.ru',
                'port'=>'143',
                'type'=>'imap',
                'ssl'=>true,
                'inBOX'=>'INBOX',
                //'processedBOX'=>'INBOX.processed',
                'processedBOX'=>false,
                'skippedBOX'=>'INBOX.skipped',
                'login'=>'1@061211.ru',
                'password'=>'iIATELQ1'
            )
        )
    )
);

return $config;