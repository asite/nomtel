<?php

return array(
    'guest' => array(
        'type' => CAuthItem::TYPE_ROLE,
    ),

    'admin' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'children' => array(
            'createSubscriptionAgreementForOwnSim',
            'updateSubscriptionAgreementForOwnSim',
            'deleteAct',
            'editNumberCard'
        )
    ),

    'support' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'children' => array(
            'updateSubscriptionAgreementForOwnSim',
            'editNumberCard'
        )
    ),

    'number' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'children' => array(
        )
    ),

    'agent' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'children' => array(
            'createSubscriptionAgreementForOwnSim',
            'deleteAct',
            'editNumberCardBySamvel'
        )
    ),

    'createSubscriptionAgreementForOwnSim' => array (
        'type' => CAuthItem::TYPE_TASK,
        'bizRule' => 'return $params["parent_agent_id"]==loggedAgentId();',
        'children' => array(
            'createSubscriptionAgreement'
        )
    ),

    'updateSubscriptionAgreementForOwnSim' => array (
        'type' => CAuthItem::TYPE_TASK,
        'bizRule' => 'return $params["parent_agent_id"]==loggedAgentId();',
        'children' => array(
            'updateSubscriptionAgreement'
        )
    ),

    'createSubscriptionAgreement' => array(
        'type' => CAuthItem::TYPE_OPERATION,
        'bizRule' => 'return $params["number_status"]==Number::STATUS_FREE;'
    ),

    'updateSubscriptionAgreement' => array(
        'type' => CAuthItem::TYPE_OPERATION,
        'bizRule' => 'return $params["number_status"]!=Number::STATUS_FREE;'
    ),

    'deleteAct'=>array(
        'type' => CAuthItem::TYPE_OPERATION,
        'bizRule' => 'return $params["act"]->agent->parent_id==loggedAgentId();'
    ),

    'editNumberCard'=>array(
        'type' => CAuthItem::TYPE_OPERATION,
    ),

    'editNumberCardBySamvel'=>array(
        'type' => CAuthItem::TYPE_OPERATION,
        'bizRule' => 'return loggedAgentId()==samvelAgentId();',
        'children' => array(
            'editNumberCard'
        )
    )
);