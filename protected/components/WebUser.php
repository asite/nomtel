<?php

class WebUser extends CWebUser
{
    function getRole() {
        return $this->getState('role');
    }

    function manyRolesAvailable() {
        return $this->getState('manyRolesAvailable');
    }

    function getAvailableRoles() {
        return array(
            'supportSuper'=>'Склад',
            'agent'=>Agent::label(),
            'cashier'=>SupportOperator::getRoleLabel('cashier')
        );
    }

    function changeRole($role) {
        if (!$this->getState('manyRolesAvailable')) return;
        switch ($role) {
            case 'supportSuper':
                $this->setState('agentId',adminAgentId());
                $this->setState('role',$role);
                break;
            case 'cashier':
                $this->setState('agentId',adminAgentId());
                $this->setState('role',$role);
                break;
            case 'agent':
                $supportOperator=SupportOperator::model()->findByPk(loggedSupportOperatorId());
                $agent=Agent::model()->findByAttributes(array('user_id'=>$supportOperator->user_id));
                if (!$agent) {
                    $agent=new Agent();
                    $agent->attributes=$supportOperator->attributes;
                    $agent->phone_1='не заполнено';
                    $agent->passport_series='НЕ';
                    $agent->passport_number='не заполнено';
                    $agent->passport_issue_date=strval(new EDateTime());
                    $agent->passport_issuer='не заполнено';
                    $agent->birth_date=strval(new EDateTime());
                    $agent->birth_place='не заполнено';
                    $agent->registration_address='не заполнено';
                    $agent->parent_id=1;
                    $agent->taking_orders=1;
                    $agent->is_agent=1;
                    $agent->is_bonus=1;
                    $agent->save();
                }
                $this->setState('agentId',$agent->id);
                $this->setState('role',$role);
                break;
            default: break;
        }
    }
}
