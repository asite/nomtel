<?php

function isAdmin()
{
    return Yii::app()->user->role=='admin';
}

function isSupport()
{
    return Yii::app()->user->role=='support';
}

function isAbonent() {
    return Yii::app()->user->role=='abonent';
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