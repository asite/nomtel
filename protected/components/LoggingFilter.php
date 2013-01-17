<?php

class LoggingFilter extends CFilter
{
    protected function preFilter()
    {
        foreach (Yii::app()->log->routes as $route) {
            if ($route instanceof CWebLogRoute || $route instanceof CProfileLogRoute) {
                $route->enabled = false;
            }
        }

        Yii::app()->db->enableProfiling = false;

        return true;
    }
}