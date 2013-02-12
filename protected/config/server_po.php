<?php

return array(
    'components'=>array(
        'urlManager'=>array(
            'rules'=>array(
                '/'=>'pOSite/login'
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