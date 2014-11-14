<?php

/**
 * SAR Corelib
 *
 * This file contains the main logic for database access code
 *
 * PHP version 5.4
 *
 * LICENSE: This source file is subject to version 2 of the GNU General Public
 * License that is avalaible in the LICENSE file on the project root directory.
 * If you did not receive a copy of the LICENSE file, please send a note to
 * 321110001@student.machung.ac.id so I can mail you a copy immidiately.
 *
 * @author Achmad Mahardi <321110001@student.machung.ac.id>
 * @copyright 2014 Achmad Mahardi
 * @license GNU General Public License v2
 * @link https://github.com/maman/sar
 */
namespace SAR\lib;
use SAR\lib\Config;
use alfmel\OCI8\PDO as OCI8;

/**
 * Core Class
 * @package SAR\lib
 */
class Core
{
    public $db;
    public $mcc;
    private static $instance;

    private function __construct() {
        $dsn = Config::read('db.dsn');
        $user = Config::read('db.username');
        $password = Config::read('db.password');
        $this->db = new OCI8($dsn, $user, $password);
    }

    /**
     * Get the current instance of this class, provides a way to use the
     * same object, given that this class is already initiated/called.
     * @return object
     */
    public static function getInstance() {
        if (!isset(self::$instance)) {
            $object = __CLASS__;
            self::$instance = new $object;
        }
        return self::$instance;
    }
}
