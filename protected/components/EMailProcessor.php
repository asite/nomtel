<?php

class EMailProcessor {
    private $imap;
    private $processedBOX;
    private $skippedBOX;
    private $uids;
    private $uid;

    public function __construct($config) {
        $ssl=$config['ssl'] ? "/novalidate-cert":"";
        $connectionString="{{$config['host']}:{$config['port']}/{$config['type']}$ssl"."}".$config['inBOX'];

        $this->imap=imap_open($connectionString,$config['login'],$config['password']);

        if ($this->imap===false) throw new CException('failed to connect to mail server');

        $this->processedBOX=$config['processedBOX'];
        $this->skippedBOX=$config['skippedBOX'];

        $this->uids=imap_search($this->imap,'UNDELETED', SE_UID);
        if ($this->uids===false) $this->uids=array();
    }

    public function __destruct() {
        if (!imap_close($this->imap)) throw new Exception('failed to disconnect from mail server');
    }

    public function fetchMail() {
        if (empty($this->uids)) return false;
        $this->uid=array_pop($this->uids);
        $res=imap_body($this->imap,$this->uid,FT_UID);

        if ($res==false) throw new CException("can't get email from server");

        return $res;
    }

    private function moveEmail($box) {
        $res=imap_mail_move($this->imap,$this->uid,$box,CP_UID);
        if ($res===false) throw new CException("can't move message to $box folder");
        //if (!imap_expunge($this->imap)) throw new CException("mail_expunge() returned false");
    }

    private function deleteMail() {
        $res=imap_delete($this->imap,$this->uid,FT_UID);
        if ($res===false) throw new CException("can't move message to $box folder");
        //if (!imap_expunge($this->imap)) throw new CException("mail_expunge() returned false");
    }

    public function EmailProcessed() {
        if ($this->processedBOX===false)
            $this->deleteMail();
        else
            $this->moveEmail($this->processedBOX);
    }

    public function EmailSkipped() {
        $this->moveEmail($this->skippedBOX);
    }
}