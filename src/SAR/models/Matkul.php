<?php

/**
 * Matkul Class for SAR Application
 *
 * this file contains the data access for Matkul objects.
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

/**
 * Matkul Class
 * @package SAR\models
 */
class Matkul
{
    private $idMatkul;
    private $syaratMatkul;
    private $namaMatkul;
    private $semesterMatkul;
    private $tahunMatkul;
    private $core;

    function __construct()
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
     * Get Mata Kuliah Details
     *
     * Get Mata Kuliah Details from KDMataKuliah
     * @param  string $id
     * @return array
     */
    public function getMatkulDetails($id)
    {
        $results = array();
        $query = $this->core->db->prepare(
            'SELECT
                *
            FROM
                "MATAKULIAH"
            WHERE
                "MATAKULIAH"."KDMataKuliah" = :id'
        );
        $query->bindParam(':id', $id);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        return $results;
    }

    /**
     * Get Mata Kuliah
     *
     * Get Mata Kuliah associated with NIP
     * @param  string $id
     * @return array
     */
    public function getMatkulByNIP($nip)
    {
        $results = array();
        $query = $this->core->db->prepare(
            'SELECT
                PLOTTING."KDMataKuliah",
                MATAKULIAH."NamaMK"
            FROM
                PLOTTING INNER JOIN MATAKULIAH
            ON
                PLOTTING."KDMataKuliah" = MATAKULIAH."KDMataKuliah"
            WHERE
                PLOTTING.NIP = :nip
                AND PLOTTING.STATUS IS NOT NULL'
        );
        $query->bindParam(':nip', $nip);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        return $results;
    }
}
