<?php

function isAdmin()
{
    return Yii::app()->user->role=='admin';
}

function isSupport()
{
    return Yii::app()->user->role=='support';
}

function isNumber() {
    return Yii::app()->user->role=='number';
}

function loggedNumberId() {
    return Yii::app()->user->getState('numberId');
}

function isPO() {
    return Yii::app()->params['PO'];
}

function loggedAgentId()
{
    return Yii::app()->user->getState('agentId');
}

function loggedSupportOperatorId()
{
    return Yii::app()->user->getState('supportOperatorId');
}

function adminAgentId()
{
    return 1;
}

function samvelAgentId()
{
    return 20;
}

function krylowAgentId() {
    return 32;
}

function isKrylow()
{
    return loggedAgentId()==krylowAgentId();
}

function isFlagParent($id, $mode) {
    if ($id==adminAgentId()) return true;
    else {
        $agent = Agent::model()->findByPk($id);
        if (!$agent->$mode) return false;
        else return isFlagParent($agent->parent_id, $mode);
    }
}

function isFlag($mode = 'is_agent') {
    if (loggedAgentId()==adminAgentId()) return true;
    $agent = Agent::model()->findByPk(loggedAgentId());
    if (!$agent->$mode) return false;

    return isFlagParent($agent->parent_id, $mode);
}

