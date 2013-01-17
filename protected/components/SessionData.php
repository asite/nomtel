<?php
/*
 * Used for passing big amount of data from form to form by special key, data is stored in session
 */
class SessionData
{
    const GC_PROBABILITY=100;
    const DEFAULT_EXPIRATION=86400;
    private $section;

    /*
     * garbage collector
     */
    private function gc() {
        if (rand(0,self::GC_PROBABILITY*10)>10) return;

        $now=time();

        foreach($_SESSION['SessionData'] as $sectionName=>$section) {
            foreach($section as $key=>$item)
                if ($item['expires']<$now) unset($_SESSION['SessionData'][$sectionName][$key]);
            if (empty($_SESSION['SessionData'][$sectionName])) unset($_SESSION['SessionData'][$sectionName]);
        }
    }

    public function __construct($section)
    {
        // force session open
        $session=new CHttpSession;
        $session->open();

        $this->section=$section;

        $this->gc();
    }

    /**
     * get data
     * @param $key key
     * @param bool $throw_exception throw exception, if data was not found
     * @return mixed data
     * @throws CHttpException
     */
    public function get($key,$throw_exception=true) {
        if ($throw_exception && !$this->exists($key)) throw new CHttpException(404,Yii::t('app','Saved data not found'));

        return $_SESSION['SessionData'][$this->section][$key]['data'];
    }

    private function generateKey($data) {
        $source=time().'_'.rand().'_'.serialize($data);
        return substr(sha1($source),0,16);
    }
    /**
     * generate key and store data under it
     * @param $data data to store
     * @param int $lifetime data lifetime
     * @return string $key data key
     */
    public function add($data,$lifetime=self::DEFAULT_EXPIRATION) {
        do {
            $key=$this->generateKey($data);
        } while ($this->exists($key));

        $this->set($key,$data,$lifetime,false);

        return $key;
    }

    /**
     * checks if key assigned with data
     * @param $key key
     * @return bool true, if data is stored
     */
    public function exists($key) {
        return isset($_SESSION['SessionData'][$this->section][$key]);
    }


    /**
     * store data by key
     * @param $key key
     * @param $data data
     * @param $lifetime data lifetime in seconds
     * @param bool $check_existence throw exception, if key has no assigned data yet
     * @throws Exception
     */
    public function set($key,$data,$lifetime=self::DEFAULT_LIFETIME,$check_existence=true) {
        if ($check_existence && !$this->exists($key)) throw new Exception('unknown key');

        $_SESSION['SessionData'][$this->section][$key]=array(
            'expires'=>time()+$lifetime,
            'data'=>$data
        );
    }

    /**
     * unset data
     * @param $key key
     */
    public function delete($key) {
        unset($_SESSION['SessionData'][$this->section][$key]);
    }
}
