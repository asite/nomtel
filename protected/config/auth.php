<?php

return array(
    'guest' => array(
        'type' => CAuthItem::TYPE_ROLE,
    ),

    'admin' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'children' => array(
            'createSubscriptionAgreementForOwnSim'
        )
    ),

    'agent' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'children' => array(
            'createSubscriptionAgreementForOwnSim'
        )
    ),

    'createSubscriptionAgreementForOwnSim' => array (
        'type' => CAuthItem::TYPE_TASK,
        'bizRule' => 'return $params["sim"]->parent_id==loggedAgentId();',
        'children' => array(
            'createSubscriptionAgreement'
        )
    ),

    'createSubscriptionAgreement' => array(
        'type' => CAuthItem::TYPE_OPERATION,
        'bizRule' => 'return !$params["sim"]->agent_id;'
    )
);