<?php

/**
 * Plotting Class for SAR Application
 *
 * this file contains the data access for External Plotting objects.
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
namespace SAR\externals;

use Slim\Slim;
use alfmel\OCI8\PDO as OCI8;
use Functional as F;
use SAR\helpers\Utilities;

class Plotting
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

    public function getAllPlotting($idProdi)
    {
        $query = $this->core->db->prepare(
            'SELECT
                PLOTTING."KDMataKuliah",
                PLOTTING.NIP
            FROM
                PLOTTING INNER JOIN MATAKULIAH
            ON
                PLOTTING."KDMataKuliah" = MATAKULIAH."KDMataKuliah"
                INNER JOIN PEGAWAI
            ON
                PLOTTING.NIP = PEGAWAI.NIP
            WHERE
                PEGAWAI.IDPRODI = :idProdi
            AND
                MATAKULIAH.IDPRODI = :idProdi
            ORDER BY
                PEGAWAI.NIP ASC'
        );
        $query->bindParam(':idProdi', $idProdi);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 0) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Get Current Plotting From Matkul ID
     * @param  string $idMatkul
     * @return string
     */
    public function getCurrentPlotting($idMatkul, $year = null)
    {
        $util = new Utilities();
        if (null === $year) {
            $year = $util->getTahunAjaran();
        }
        $query = $this->core->db->prepare(
            'SELECT
                "ID_PLOTTING"
            FROM
                PLOTTING
            WHERE
                "KDMataKuliah" = :idMatkul
            AND
                TAHUNAJARAN = :currYear'
        );
        $query->bindParam(':idMatkul', $idMatkul);
        $query->bindParam(':currYear', $year);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 0) {
            return $results[0]['ID_PLOTTING'];
        } else {
            return false;
        }
    }

    /**
     * Get all year from plotting table
     * @return mixed
     */
    public function getAllPlottingYearByMatkul($idMatkul)
    {
        $query = $this->core->db->prepare(
            'SELECT DISTINCT
                "TAHUNAJARAN"
            FROM
                PLOTTING
            WHERE
                "KDMataKuliah" = :idMatkul'
        );
        $query->bindParam(':idMatkul', $idMatkul);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 0) {
            return $results;
        } else {
            return false;
        }
    }
}
