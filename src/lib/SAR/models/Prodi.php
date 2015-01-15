<?php

/**
 * Prodi Class for SAR Application
 *
 * this file contains the data access for Prodi objects.
 *
 * PHP version 5.4
 *
 * LICENSE: This source file is subject to version 2 of the GNU General Public
 * License that is avalaible in the LICENSE file on the project root directory.
 * If you did not receive a copy of the LICENSE file, please send a note to
 * 321110001@student.machung.ac.id so I can mail you a copy immidiately.
 *
 * @package SAR\models
 * @author Achmad Mahardi <321110001@student.machung.ac.id>
 * @copyright 2014 Achmad Mahardi
 */
namespace SAR\models;

use Slim\Slim;
use alfmel\OCI8\PDO as OCI8;
use Functional as F;

/**
 * SelfAssest Class
 * @package SAR\models
 */
class Prodi
{
    private $core;

    public function __construct()
    {
        $this->core = Slim::getInstance();
    }

    public function __get($prop)
    {
        if (property_exists($this, $prop)) {
            return $this->$prop;
        }
    }

    public function __set($prop, $val)
    {
        if (property_exists($this, $prop)) {
            $this->$prop = $val;
        }
        return $this;
    }

    /**
     * Get All Prodi
     * @return array
     */
    public function getAllProdi()
    {
        $query = $this->core->db->prepare('
            SELECT
                *
            FROM
                PRODI
        ');
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 0) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Get Prodi Detail by IDProdi
     * @param  string $idProdi
     * @return mixed
     */
    public function getProdiDetails($idProdi)
    {
        $result = $this->getAllProdi();
        if ($result) {
            $results = F\select($result, function ($item, $key, $col) use ($idProdi) {
                return $item['IDProdi'] == $idProdi;
            });
            if (count($results) == 1) {
                return $results[0];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
