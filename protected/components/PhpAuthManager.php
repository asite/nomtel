<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pavel
 * Date: 02.01.13
 * Time: 17:26
 * To change this template use File | Settings | File Templates.
 */
class PhpAuthManager extends CPhpAuthManager
{
    public function init() {
        parent::init();

        $this->assign(loggedAgentId()==adminAgentId() ? 'admin':'agent',loggedAgentId());
    }
}
