<?php

function isAdmin()
{
    return Yii::app()->user->getState('isAdmin');
}

function loggedAgentId()
{
    return Yii::app()->user->getState('agentId');
}

function adminAgentId()
{
    return 1;
}