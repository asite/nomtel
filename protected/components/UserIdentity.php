<?php

class UserIdentity extends CUserIdentity
{

    private $_id;

    public function authenticate()
    {
        $result = User::model()->login($this->username, $this->password);
        if ($result instanceof User) {
            $this->_id = $result->id;
            $agent = Agent::model()->findByAttributes(array('user_id' => $this->_id));
            if ($agent) {
                $this->setState('agentId', $agent->id);
            }
            $supportOperator = SupportOperator::model()->findByAttributes(array('user_id' => $this->_id));
            if ($agent) {
                $this->setState('supportOperatorId', $supportOperator->id);
                $this->setState('agentId', adminAgentId());
            }

            return true;
        } else {
            $this->errorMessage = $result;
            return false;
        }
    }

    public function getId()
    {
        return $this->_id;
    }
}

