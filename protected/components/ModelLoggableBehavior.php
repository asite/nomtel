<?php

class ModelLoggableBehavior extends CModelBehavior
{
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_BLOCKED = 'BLOCKED';

    private $maxFailedLogins = 3;
    private $blockTime = 15;
    private $authIdentityModelAttrs = 'user';
    private $authIdentityClass = 'ModelBasedIdentity';

    public function setAuthComponentName($authComponentName)
    {
        $this->authComponentName = $authComponentName;
    }

    public function getAuthComponentName()
    {
        return $this->authComponentName;
    }

    public function setAuthIdentityClass($authIdentityClass)
    {
        $this->authIdentityClass = $authIdentityClass;
    }

    public function getAuthIdentityClass()
    {
        return $this->authIdentityClass;
    }

    private function blowfishSalt($cost = 13)
    {
        if (!is_numeric($cost) || $cost < 4 || $cost > 31) {
            throw new CException("cost parameter must be between 4 and 31");
        }
        $rand = array();
        for ($i = 0; $i < 8; $i += 1) {
            $rand[] = pack('S', mt_rand(0, 0xffff));
        }
        $rand[] = substr(microtime(), 2, 6);
        $rand = sha1(implode('', $rand), true);
        $salt = '$2a$' . str_pad((int)$cost, 2, '0', STR_PAD_RIGHT) . '$';
        $salt .= strtr(substr(base64_encode($rand), 0, 22), array('+' => '.'));
        return $salt;
    }

    private function _encryptPwd($salt, $password)
    {
        return crypt($password, $salt);
    }

    public function encryptPwd()
    {
        $this->owner->password = $this->_encryptPwd($this->blowfishSalt(), $this->owner->password);
    }

    public function login($username, $password)
    {
        $user = $this->owner->findByAttributes(array('username' => $username));

        if (!$user)
            return Yii::t('app', 'Invalid username/password');

        if ($user->status == self::STATUS_BLOCKED)
            return Yii::t('app', 'Your account blocked by admin');

        if ($user->blocked_until > new EDateTime('now'))
            return Yii::t('app', 'Your account blocked for :m minutes due to invalid logins.', array(':m' => $this->blockTime));

        if ($this->_encryptPwd($user->password, $password) != $user->password) {
            $user->failed_logins += 1;

            if ($user->failed_logins >= $this->maxFailedLogins) {
                $user->blocked_until = new EDateTime("+" . $this->blockTime . " min");
                $user->failed_logins = 0;
            }

            $user->save();
            return Yii::t('app', 'Invalid username/password');
        }

        if ($user->failed_logins > 0) {
            $user->failed_logins = 0;
            $user->blocked_until = null; //new CDbExpression("NULL");
            $user->save();
        }

        return $user;
    }

    public function logout()
    {
        $authComponentName = $this->authComponentName;
        Yii::app()->$authComponentName->logout(false);
    }

}