<?php

return array(
    'components'=>array(
        'urlManager'=>array(
            'rules'=>array(
                '/'=>'site/loginPO'
            )
        ),
        'user'=>array(
            'loginUrl'=>'/'
        )
    ),
    'params'=>array(
        'PO'=>true
    )
);