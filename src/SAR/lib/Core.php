<?php

namespace SAR\lib;

use SAR\lib\Config;
use CrazyCodr\Pdo\Oci8;

class Core {
    public $db;
    public $mcc;
    private static $instance;

    private function __construct() {
        $dsn = Config::read('db.dsn');
        $user = Config::read('db.user');
        $password = Config::read('db.password');
        $mccHost = Config::read('memcached.host');
        $mccPort = Config::read('memcached.port');
        $mccStub = new Memcached;
        $this->mcc = $mccStub->connect($mccHost, $mccPort);
        $this->db = new Oci8($dsn, $user, $password);
    }

    public function getInstance() {
        if (!isset(self::$instance)) {
            $object = __CLASS__;
            self::$instance = new $object;
        }
        return self::$instance;
    }
}
