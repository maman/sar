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
use SAR\models\Rps;
use SAR\helpers\Utilities;

/**
 * Matkul Class
 * @package SAR\helpers
 */
class Matkul
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
     * Get All Recorded matakuliah
     * @param  boolean $current limit year range. defaults to true.
     * @return array
     */
    public function getAllMatkul($current = true)
    {
        $tahun = new Utilities();
        $range = $tahun->getRangeTahunAjaran();
        $results = array();
        if ($current) {
            $query = $this->core->db->prepare(
                'SELECT
                    *
                FROM
                    "MATAKULIAH"
                WHERE
                    "MATAKULIAH"."TahunAjaranMK"
                    BETWEEN :tahunMulai
                    AND :tahunSelesai'
            );
            $query->bindParam(':tahunMulai', $range['start']);
            $query->bindParam(':tahunSelesai', $range['end']);
        } else {
            $query = $this->core->db->prepare(
                'SELECT
                    *
                FROM
                    "MATAKULIAH"'
            );
        }
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        return $results;
    }

    /**
     * Get Mata Kuliah Details
     *
     * Get Mata Kuliah Details from KDMataKuliah
     * @param  string  $id
     * @param  boolean $current limit year range. defaults to true.
     * @return array
     */
    public function getMatkulDetails($id, $current = true)
    {
        $tahun = new Utilities();
        $range = $tahun->getRangeTahunAjaran();
        $results = array();
        if ($current) {
            $query = $this->core->db->prepare(
                'SELECT
                    *
                FROM
                    "MATAKULIAH"
                WHERE
                    "MATAKULIAH"."KDMataKuliah" = :id
                    AND "MATAKULIAH"."TahunAjaranMK"
                    BETWEEN :tahunMulai
                    AND :tahunSelesai'
            );
            $query->bindParam(':id', $id);
            $query->bindParam(':tahunMulai', $range['start']);
            $query->bindParam(':tahunSelesai', $range['end']);
        } else {
            $query = $this->core->db->prepare(
                'SELECT
                    *
                FROM
                    "MATAKULIAH"
                WHERE
                    "MATAKULIAH"."KDMataKuliah" = :id'
            );
            $query->bindParam(':id', $id);
        }
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
        $tahun = new Utilities();
        $range = $tahun->getRangeTahunAjaran();
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
                AND PLOTTING.STATUS IS NOT NULL
                AND "MATAKULIAH"."TahunAjaranMK"
                BETWEEN :tahunMulai
                AND :tahunSelesai'
        );
        $query->bindParam(':nip', $nip);
        $query->bindParam(':tahunMulai', $range['start']);
        $query->bindParam(':tahunSelesai', $range['end']);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        return $results;
    }

    /**
     * isMatkulNIP
     *
     * Check if current user is handed the provided Mata Kuliah
     * @param  string  $nip
     * @param  string  $idMatkul
     * @return boolean
     */
    public function isMatkulNIP($nip, $idMatkul)
    {
        $results = false;
        $query = $this->core->db->prepare(
            'SELECT
                "KDMataKuliah",
                NIP,
                STATUS
            FROM
                PLOTTING
            WHERE
                "KDMataKuliah" = :idMatkul
            AND
                NIP = :nip
            AND
                STATUS IS NOT NULL'
        );
        $query->bindParam(':nip', $nip);
        $query->bindParam(':idMatkul', $idMatkul);
        $query->execute();
        $results = $query->fetchColumn();
        if ($results > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get Matakuliah name from idMatkul
     * @param  string  $idMatkul
     * @param  boolean $current  defaults to true
     * @return string
     */
    public function getMatkulName($idMatkul, $current = true)
    {
        $results = $this->getMatkulDetails($idMatkul, $current);
        if ($results) {
            return $results[0]['NamaMK'];
        } else {
            return 'Not Defined';
        }
    }

    /**
     * Get Matakuliah by year
     * @return array
     */
    public function getMatkulYear()
    {
        $query = $this->core->db->prepare(
            'SELECT DISTINCT
                MATAKULIAH."TahunAjaranMK"
            FROM MATAKULIAH'
        );
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        return $results;
    }

    /**
     * Get Matakuliah count by Year
     * @param  string $year in Y format
     * @return int
     */
    public function getMatkulYearCount($year)
    {
        $query = $this->core->db->prepare(
            'SELECT
                *
            FROM
                MATAKULIAH
            WHERE
                "TahunAjaranMK" = :yearData'
        );
        $query->bindParam(':yearData', $year);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        return count($results);
    }
}
