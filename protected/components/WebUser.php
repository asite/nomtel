<?php

class WebUser extends CWebUser
{
    private $role = null;

    function getRole() {
        if ($this->role===null) {
            $agent=Agent::model()->findByAttributes(array('user_id'=>$this->id));
            if ($agent) {
                $this->role=$agent->id==adminAgentId() ? 'admin':'agent';
            } else {
                $supportOperator=SupportOperator::model()->findByAttributes(array('user_id'=>$this->id));
                if ($supportOperator) {
                    $this->role='support';
                }
            }
        }
        return $this->role;
    }
}
