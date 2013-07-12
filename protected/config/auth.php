<?php

return array(
    'guest' => array(
        'type' => CAuthItem::TYPE_ROLE,
    ),

    'admin' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'children' => array(
            'createSubscriptionAgreementForOwnSim',
            'updateSubscriptionAgreement',
            'deleteAct',
            'editNumberCard'
        )
    ),

    'support' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'children' => array(
            'updateSubscriptionAgreement',
            'editNumberCard'
        )
    ),

    'supportMain' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'children' => array(
            'support',
        )
    ),

    'supportAdmin' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'children' => array(
            'support',
        )
    ),

    'supportMegafon' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'children' => array(
            'updateSubscriptionAgreementForMegafonNumber'
        )
    ),

    'supportBeeline' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'children' => array(
            'updateSubscriptionAgreementForBeelineNumber'
        )
    ),

    'supportSuper' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'children' => array(
            'admin'
        )
    ),

    'cashier' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'children' => array(
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
            'updateSubscriptionAgreement',
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

    'updateSubscriptionAgreementForMegafonNumber' => array (
        'type' => CAuthItem::TYPE_TASK,
        'bizRule' => 'return $params["number"]->sim->operator_id==Operator::OPERATOR_MEGAFON_ID;',
        'children' => array(
            'updateSubscriptionAgreement'
        )
    ),

    'updateSubscriptionAgreementForBeelineNumber' => array (
        'type' => CAuthItem::TYPE_TASK,
        //'bizRule' => 'return $params["number"]->sim->operator_id==Operator::OPERATOR_MEGAFON_ID;',
        'children' => array(
            'updateSubscriptionAgreement'
        )
    ),

    'createSubscriptionAgreement' => array(
        'type' => CAuthItem::TYPE_OPERATION,
        'bizRule' => 'return in_array($params["number_status"],array(Number::STATUS_FREE,Number::STATUS_UNKNOWN));'
    ),

    'updateSubscriptionAgreement' => array(
        'type' => CAuthItem::TYPE_OPERATION,
        'bizRule' => 'return !in_array($params["number_status"],array(Number::STATUS_FREE,Number::STATUS_UNKNOWN));'
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