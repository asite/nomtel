<?php

class WebUser extends CWebUser
{
    function getRole() {
        return $this->getState('role');
    }
}
