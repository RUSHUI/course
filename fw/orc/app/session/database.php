<?php
namespace ORC\APP\Session;
use ORC\Core\Config;
use ORC\DBAL\DBAL;
use ORC\Util\Util;
use ORC\Application;
class Database extends Session {
    private $session_started = false;
    private $table;
    private $hashkey = 'defaulthashkey';
    private $session_id;
    private $cookie_key;
    
    public function __construct() {
        $config = Config::getInstance();
        $tablename = $config->get('app.session.tablename');
        if (!$tablename) {
            $tablename = 'sessions';
        }
        $this->table = $tablename;
        $this->cookie_key = sprintf('%s_sid', Application::getApp()->getName());
        parent::__construct();
    }
 /* (non-PHPdoc)
     * @see \ORC\APP\Session\Session::getId()
     */
    public function getId()
    {
        if (isset($this->session_id)) {
            return $this->session_id;
        }
        $this->retrieveSessionId();
        return $this->session_id;
    }

 /* (non-PHPdoc)
     * @see \ORC\APP\Session\Session::onShutDown()
     */
    public function onShutDown()
    {
        //save to db
        $dbal = DBAL::insert($this->table);
        $dbal->set('session_id', $this->getId());
        $dbal->set('value', gzcompress(serialize($this->getAllData())));
        $dbal->set('created', Util::getNow());
        $dbal->set('updated', Util::getNow());
        $dbal->setDuplicate(array('value', 'updated'));
        $dbal->execute();
    }

 /* (non-PHPdoc)
     * @see \ORC\APP\Session\Session::start()
     */
    protected function start()
    {
        if ($this->started()) {
            return true;
        }
        $dbal = DBAL::select($this->table);
        $dbal->bySessionId($this->getId());
        $row = $dbal->getOne();
        if ($row) {
            $data = @unserialize(gzuncompress($row['value']));
            if (is_array($data)) {
                $this->_data = $data;
            }
        }
        $this->session_started = true;
    }

 /* (non-PHPdoc)
     * @see \ORC\APP\Session\Session::started()
     */
    public function started()
    {
        return $this->session_started;
    }

    protected function retrieveSessionId() {
        $session_id = @$_COOKIE[$this->cookie_key];
        if (empty($session_id)) {
            $session_id = $this->createNewId();
            setcookie($this->cookie_key, $session_id);
            $_COOKIE[$this->cookie_key] = $session_id;
        }
        $this->session_id = $session_id;
    }
    
    protected function createNewId() {
        return Util::generateRandStr(40);
    }
    
}