<?php

/**
 * Plotting Class for SAR Application
 *
 * this file contains the data access for Plotting objects.
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
 * Plotting Class
 * @package SAR\models
 */
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

    /**
     * Get All Plotting Based on ID Prodi
     * @param  string $idProdi
     * @return mixed
     */
    public function getAllPlotting($idProdi)
    {
        $query = $this->core->db->prepare('
            SELECT
                PLOTTING_KOMPETENSI.*
            FROM
                PLOTTING_KOMPETENSI INNER JOIN PEGAWAI
            ON
                PLOTTING_KOMPETENSI.NIP = PEGAWAI.NIP
                INNER JOIN MATAKULIAH
            ON
                PLOTTING_KOMPETENSI."KDMataKuliah" = MATAKULIAH."KDMataKuliah"
            WHERE
                PEGAWAI.IDPRODI = :idProdi
            AND
                MATAKULIAH.IDPRODI = :idProdi
        ');
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
     * Get Matakuliah for Provided NIP
     * @param  string $nip
     * @return mixed
     */
    public function getDosenMatkul($nip, $idProdi)
    {
        $results = $this->getAllPlotting($idProdi);
        if ($results) {
            $result = F\select($results, function ($item, $key, $col) use ($nip) {
                return $item['NIP'] == $nip;
            });
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Get Dosen for Provided ID Matakuliah
     * @param  string $idMatkul
     * @param  string $idProdi
     * @return string
     */
    public function getDosenByMatkul($idMatkul, $idProdi)
    {
        $results = $this->getAllPlotting($idProdi);
        if ($results) {
            $result = F\select($results, function ($item, $key, $col) use ($idMatkul) {
                return $item['KDMataKuliah'] == $idMatkul;
            });
            if (count($result) == 1) {
                $val = reset($result);
                return $val['NIP'];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Get Unplotted Dosen
     * @param  string $idProdi
     * @return mixed
     */
    public function getUnplottedDosen($idProdi)
    {
        $query = $this->core->db->prepare('
            SELECT
                PEGAWAI.NIP
            FROM
                PEGAWAI LEFT JOIN PLOTTING_KOMPETENSI
            ON
                PEGAWAI.NIP = PLOTTING_KOMPETENSI.NIP
            WHERE
                PLOTTING_KOMPETENSI."KDMataKuliah" IS NULL
            AND
                PEGAWAI.TGLKELUAR IS NULL
            AND
                PEGAWAI.IDPRODI = :idProdi
        ');
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
     * Get Unplotted Matkul
     * @param  string $idProdi
     * @return mixed
     */
    public function getUnplottedMatkul($idProdi)
    {
        $query = $this->core->db->prepare('
            SELECT
                MATAKULIAH."KDMataKuliah"
            FROM
                MATAKULIAH LEFT JOIN PLOTTING_KOMPETENSI
            ON
                MATAKULIAH."KDMataKuliah" = PLOTTING_KOMPETENSI."KDMataKuliah"
            WHERE
                PLOTTING_KOMPETENSI."KDMataKuliah" IS NULL
                AND MATAKULIAH.IDPRODI = :idProdi
        ');
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
     * Save Plotting Entry
     * @param  string $nip
     * @param  string $idMatkul
     * @return boolean
     */
    public function savePlotting($nip, $idMatkul)
    {
        try {
            $query = $this->core->db->prepare(
                'INSERT INTO
                    PLOTTING_KOMPETENSI
                (
                    NIP,
                    "KDMataKuliah"
                )
                VALUES
                (
                    :nip,
                    :idMatkul
                )'
            );
            $query->bindParam(':nip', $nip);
            $query->bindParam(':idMatkul', $idMatkul);
            $query->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete Plotting Entry
     * @param  string $nip
     * @return boolean
     */
    public function deletePlotting($idMatkul)
    {
        try {
            $query = $this->core->db->prepare(
                'DELETE FROM
                    PLOTTING_KOMPETENSI
                WHERE
                    "KDMataKuliah" = :idMatkul'
            );
            $query->bindParam(':idMatkul', $idMatkul);
            $query->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
