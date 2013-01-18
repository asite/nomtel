<?php

function isAdmin()
{
    return Yii::app()->user->role=='admin';
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