<?php

return array(
    'guest' => array(
        'type' => CAuthItem::TYPE_ROLE,
    ),

    'admin' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'children' => array(
            'createSubscriptionAgreementForOwnSim',
            'deleteAct'
        )
    ),

    'support' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'children' => array(
        )
    ),

    'agent' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'children' => array(
            'createSubscriptionAgreementForOwnSim',
            'deleteAct'
        )
    ),

    'createSubscriptionAgreementForOwnSim' => array (
        'type' => CAuthItem::TYPE_TASK,
        'bizRule' => 'return $params["parent_agent_id"]==loggedAgentId();',
        'children' => array(
            'createSubscriptionAgreement'
        )
    ),

    'createSubscriptionAgreement' => array(
        'type' => CAuthItem::TYPE_OPERATION,
        'bizRule' => 'return $params["number_status"]==Number::STATUS_FREE;'
    ),

    'deleteAct'=>array(
        'type' => CAuthItem::TYPE_OPERATION,
        'bizRule' => 'return $params["act"]->agent->parent_id==loggedAgentId();'
    )
);