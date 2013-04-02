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
            'adminPhone' => '9292000003',
            'adminEmailFrom' => 'info@asiteapp.ru',
            'numberHelpEmail' => 'alegudmail@gmail.com',
            'supportEmail' => array('alegudmail@gmail.com', '1@500099.ru'),
            'supportEmailFrom' => 'info@asiteapp.ru',
            'megafonBalanceEmails' => array(
                'host'=>'mail.asiteplace.ru',
                'port'=>'143',
                'type'=>'imap',
                'ssl'=>true,
                'inBOX'=>'INBOX',
                //'processedBOX'=>'INBOX.processed',
                'processedBOX'=>false,
                'skippedBOX'=>'INBOX.skipped',
                'login'=>'robot@asiteplace.ru',
                'password'=>'XWfVmXW4'
            )
        )
    )
);

return $config;