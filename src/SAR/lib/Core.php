<?php

namespace SAR\lib;

use SAR\lib\Config;
use alfmel\OCI8\PDO;
// use Memcached;

/**
 * Core Library for Database & Cache
 * Access
 */
class Core {
    public $db;
    public $mcc;
    private static $instance;

    private function __construct() {
        $dsn = Config::read('db.dsn');
        $user = Config::read('db.username');
        $password = Config::read('db.password');
        // $mccHost = Config::read('memcached.host');
        // $mccPort = Config::read('memcached.port');
        // $mccStub = new Memcached;
        // $this->mcc = $mccStub->addServer($mccHost, $mccPort);
        $this->db = new \alfmel\OCI8\PDO($dsn, $user, $password);
    }

    public static function getInstance() {
        if (!isset(self::$instance)) {
            $object = __CLASS__;
            self::$instance = new $object;
        }
        return self::$instance;
    }
}
