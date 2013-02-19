<?php

class EMailProcessor {
    private $imap;
    private $cachedMails;

    public function __construct($config) {
        $ssl=$config['ssl']==false ? "/novalidate-cert":"";
        $this->imap_open("{$config['host']}:{$config['port']}/{$config['type']}$ssl"."}INBOX",$config['login'],$config['password']);

        if ($this->imap===false) throw new Exception('failed to connect to mail server');
    }

    public function __destruct() {
        if (!imap_close($this->imap)) throw new Exception('failed to disconnect from mail server');
    }

    public function fetchMail() {

    }

    public function deleteMail() {

    }

    public function skipEmail() {

    }
}