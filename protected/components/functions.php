<?php

function isAdmin()
{
    return Yii::app()->user->role=='admin';
}

function loggedAgentId()
{
    return Yii::app()->user->getState('agentId');
}

function adminAgentId()
{
    return 1;
}