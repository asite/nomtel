<?php

class PhpAuthManager extends CPhpAuthManager
{
    public function init()
    {
        parent::init();

        $this->assign(loggedAgentId() == adminAgentId() ? 'admin' : 'agent', loggedAgentId());
    }
}
