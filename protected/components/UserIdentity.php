<?php

class UserIdentity extends CUserIdentity {

    private $_id;

    public function authenticate() {
        $result = User::model()->login($this->username, $this->password);
        if ($result instanceof User) {
            $this->_id = $result->id;
            return true;
        } else {
            $this->errorMessage = $result;
            return false;
        }
    }

    public function getId() {
        return $this->_id;
    }

}

