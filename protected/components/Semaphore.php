<?php

class Semaphore {
    private $_semId;
    private $_name;

    private function ftok($name) {
        $sum= sha1($name, true);
        return (ord('k')<<24)+(ord($sum[0])<<16)+(ord($sum[1])<<8)+ord($sum[2]);
    }

    public function __construct($name,$maxAcquires) {
        $this->_semId=sem_get($this->ftok($name),$maxAcquires);
        $this->_name=$name;
        if ($this->_semId===false)
            throw new CException(Yii::t('app',"Can't get semaphore ':name'",array(':name'=>$this->_name)));
        $this->acquire();
    }

    public function acquire() {
        if (!sem_acquire($this->_semId))
            throw new CException(Yii::t('app',"Can't acquire semaphore ':name'",array(':name'=>$this->_name)));
    }

    public function release() {
        if (!sem_release($this->_semId))
            throw new CException(Yii::t('app',"Can't release semaphore ':name'",array(':name'=>$this->_name)));
    }

    public function __destruct() {
        $this->release();
    }
}