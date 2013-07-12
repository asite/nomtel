<?php

class UserIdentity extends CUserIdentity
{

    private $_id;

    public function authenticate()
    {
        $result = User::model()->login($this->username, $this->password);
        if ($result instanceof User) {
            $this->_id = $result->id;
            if (isPO()) {
                $number = Number::model()->findByAttributes(array('user_id' => $this->_id));
                if ($number) {
                    $this->setState('numberId', $number->id);
                    $this->setState('role','number');
                    return true;
                }
            } else {
                $agent = Agent::model()->findByAttributes(array('user_id' => $this->_id));
                if ($agent) {
                    $this->setState('agentId', $agent->id);
                    $this->setState('role',$agent->id==adminAgentId() ? 'admin':'agent');
                    $this->setState('username',$agent->user->username);
                    if ($agent->id==adminAgentId())
                        $this->setState('supportOperatorId',SupportOperator::OPERATOR_ADMIN_ID);
                    return true;
                }
                $supportOperator = SupportOperator::model()->findByAttributes(array('user_id' => $this->_id));
                if ($supportOperator) {
                    $this->setState('manyRolesAvailable',$supportOperator->role=='supportSuper');
                    $this->setState('supportOperatorId', $supportOperator->id);
                    $this->setState('agentId', adminAgentId());
                    $this->setState('role',$supportOperator->role);
                    $this->setState('username',$supportOperator->user->username);
                    return true;
                }
            }

            Yii::t('app', 'Invalid username/password');
            return false;
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

